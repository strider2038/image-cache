<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Core\ControllerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
use Strider2038\ImgCache\Core\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Application
{
    private const LOGGER_ID = 'logger';
    private const REQUEST_ID = 'request';
    private const ROUTER_ID = 'router';
    private const RESPONSE_FACTORY_ID = 'response_factory';
    private const RESPONSE_SENDER_ID = 'response_sender';

    /** @var ContainerInterface */
    private $container;

    /** @var ResponseSenderInterface */
    private $responseSender;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var RequestInterface */
    private $request;

    /** @var RouterInterface */
    private $router;

    /** @var LoggerInterface */
    private $logger;

    /** @var bool */
    private $ready = false;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        if (!$this->container->has(self::LOGGER_ID)) {
            $this->logger = new NullLogger();
        } else {
            $this->logger = $this->container->get(self::LOGGER_ID);
        }

        register_shutdown_function([$this, 'onShutdown'], $this->logger);

        try {
            $this->responseSender = $this->container->get(self::RESPONSE_SENDER_ID);
            $this->responseFactory = $this->container->get(self::RESPONSE_FACTORY_ID);
            $this->request = $this->container->get(self::REQUEST_ID);
            $this->router = $this->container->get(self::ROUTER_ID);
            $this->ready = true;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            header('HTTP/1.1 500 Internal server error');
        }
    }
    
    public function run(): int 
    {
        if (!$this->ready) {
            return 1;
        }

        $exitCode = 0;
        try {
            $this->logger->debug('Application started');

            $route = $this->router->getRoute($this->request);
            /** @var ControllerInterface $controller */
            $controller = $this->container->get($route->getControllerId());
            $response = $controller->runAction($route->getActionId(), $route->getLocation());
            $this->responseSender->send($response);

            $this->logger->debug(sprintf(
                'Application ended. Response %d is sent',
                $response->getStatusCode()->getValue()
            ));
        } catch (\Exception $exception) {
            $exitCode = 1;
            $this->logger->error($exception);

            $response = $this->responseFactory->createExceptionResponse($exception);
            $this->responseSender->send($response);
        }

        return $exitCode;
    }

    public static function onShutdown(LoggerInterface $logger = null, array $error = null): void
    {
        $logger = $logger ?? new NullLogger();

        $error = $error ?? error_get_last();

        if ($error !== null) {
            $message = implode(PHP_EOL, array_map(
                function ($value, $key) {
                    return sprintf('%s: %s', ucfirst($key), $value);
                },
                $error,
                array_keys($error)
            ));

            $logger->critical($message);
        }
    }
}
