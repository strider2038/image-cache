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
use Strider2038\ImgCache\Core\DeprecatedResponseInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Route;
use Strider2038\ImgCache\Core\RouterInterface;
use Strider2038\ImgCache\Response\ExceptionResponse;
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

            /** @var RequestInterface $request */
            $request = $this->container->get(self::REQUEST_ID);

            /** @var RouterInterface $router */
            $router = $this->container->get(self::ROUTER_ID);

            /** @var Route $route */
            $route = $router->getRoute($request);

            /** @var ControllerInterface $controller */
            $controller = $this->container->get($route->getControllerId());
     
            /** @var DeprecatedResponseInterface $response */
            $response = $controller->runAction($route->getActionId(), $route->getLocation());
            
            $response->send();

            $this->logger->debug(sprintf('Application ended. Response %d is sent', $response->getHttpCode()));

        } catch (\Exception $exception) {

            $response = new ExceptionResponse($exception, $this->isDebugMode());
            $this->logger->error($response->getMessage());
            $response->send();

            return 1;
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