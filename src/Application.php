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
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
use Strider2038\ImgCache\Core\Route;
use Strider2038\ImgCache\Core\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Application
{
    private const CONFIG_DEBUG = 'app.debug';
    private const LOGGER_ID = 'logger';
    private const REQUEST_ID = 'request';
    private const ROUTER_ID = 'router';
    private const RESPONSE_FACTORY_ID = 'responseFactory';
    private const RESPONSE_SENDER_ID = 'responseSender';

    /** @var ContainerInterface */
    private $container;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        if (!$this->container->has(self::LOGGER_ID)) {
            $this->logger = new NullLogger();
        } else {
            $this->logger = $this->container->get(self::LOGGER_ID);
        }
    }
    
    public function run(): int 
    {
        try {

            $this->logger->debug('Application started');

            /** @var ResponseSenderInterface $responseSender */
            $responseSender = $this->container->get(self::RESPONSE_SENDER_ID);

            /** @var ResponseFactoryInterface $responseFactory */
            $responseFactory = $this->container->get(self::RESPONSE_FACTORY_ID);

        } catch (\Exception $exception) {
            $this->logger->critical($exception);

            return 1;
        }

        try {

            /** @var RequestInterface $request */
            $request = $this->container->get(self::REQUEST_ID);

            /** @var RouterInterface $router */
            $router = $this->container->get(self::ROUTER_ID);

            /** @var Route $route */
            $route = $router->getRoute($request);

            /** @var ControllerInterface $controller */
            $controller = $this->container->get($route->getControllerId());
     
            /** @var ResponseInterface $response */
            $response = $controller->runAction($route->getActionId(), $route->getLocation());

        } catch (\Exception $exception) {

            $this->logger->error($exception);

            $response = $responseFactory->createExceptionResponse($exception);

        } finally {

            $responseSender->send($response);

            $this->logger->debug(sprintf(
                'Application ended. Response %d is sent',
                $response->getStatusCode()->getValue()
            ));

        }

        return 0;
    }

    public function isDebugMode(): bool
    {
        if (!$this->container->hasParameter(self::CONFIG_DEBUG)) {
            return false;
        }

        return (bool) $this->container->getParameter(self::CONFIG_DEBUG);
    }
}