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
use Strider2038\ImgCache\Core\SecurityInterface;
use Strider2038\ImgCache\Exception\ApplicationException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 * @property RequestInterface $request Web request object
 * @property SecurityInterface $security Security control component
 */
class Application
{
    /** @var string */
    private $id;
    
    /** @var ComponentsContainer */
    private $components;
    
    /** @var array */
    private $params = [];

    /**
     * @param array $config
     * @throws ApplicationException
     */
    public function __construct(array $config) 
    {
        if (!isset($config['id']) || !is_string($config['id'])) {
            throw new ApplicationException('Empty application id');
        }
        $this->id = $config['id'];
        
        $components = array_replace(
            $this->getCoreComponents(),
            $config['components'] ?? []
        );
        
        $this->components = new Core\ComponentsContainer(
            $this,
            $components
        );
        
        if (isset($config['params']) && is_array($config['params'])) {
            $this->params = $config['params'];
        }
    }
    
    public function __get($name) 
    {
        return $this->components->get($name);
    }
    
    /**
     * @return string
     */
    public function getId(): string 
    {
        return $this->id;
    }
    
    public function run(): int 
    {
        try {
            /** @var RequestInterface */
            $request = $this->components->get('request');

            /** @var RouterInterface */
            $router = $this->components->get('router');

            /** @var Route */
            $route = $router->getRoute($request);

            /** @var ControllerInterface */
            $controller = $route->getController();
     
            /** @var ResponseInterface */
            $response = $controller->runAction($route->getAction(), $request);
            
            $response->send();
        } catch (\Exception $ex) {
            $response = new Response\ExceptionResponse($this, $ex);
            $response->send();
        }
        return 0;
    }
    
    private function getCoreComponents(): array 
    {
        return [
            'request' => Core\Request::class,
            'security' => function(Application $app) {
                return new Core\Security($app->request, $app->getParam('securityToken'));
            },
        ];
    }
    
    public function getParam(string $key)
    {
        if (!isset($this->params[$key])) {
            throw new ApplicationException("Parameter '$key' is not set");
        }
        return $this->params[$key];
    }
    
    public function isDebugMode(): bool
    {
        return !empty($this->params['debug']);
    }
}