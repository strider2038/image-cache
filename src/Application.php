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

use Strider2038\ImgCache\Core\ComponentsContainer;
use Strider2038\ImgCache\Core\ControllerInterface;
use Strider2038\ImgCache\Core\RequestInterface;
use Strider2038\ImgCache\Core\ResponseInterface;
use Strider2038\ImgCache\Core\Route;
use Strider2038\ImgCache\Core\RouterInterface;
use Strider2038\ImgCache\Response\ExceptionResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Application
{
    const CONFIG_DEBUG = 'app.debug';

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function run(): int 
    {
        try {
            /** @var RequestInterface $request */
            $request = $this->container->get('request');

            /** @var RouterInterface $router */
            $router = $this->container->get('router');

            /** @var Route $route */
            $route = $router->getRoute($request);

            /** @var ControllerInterface $controller */
            $controller = $this->container->get($route->getControllerId());
     
            /** @var ResponseInterface $response */
            $response = $controller->runAction($route->getActionId(), $route->getLocation());
            
            $response->send();
        } catch (\Exception $ex) {
            $response = new ExceptionResponse($ex, $this->isDebugMode());
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