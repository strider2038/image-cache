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

use Strider2038\ImgCache\Exception\ApplicationException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 * @property Core\RequestInterface $request Web request object
 * @property Core\SecurityInterface $security Security control component
 */
class Application
{
    /** @var string */
    private $id;

    /** @var Core\ComponentsContainer */
    private $components;

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
    
    public function run(): void 
    {
        try {
            /** @var \Strider2038\ImgCache\Core\RequestInterface */
            $request = $this->components->get('request');

            /** @var \Strider2038\ImgCache\Core\RouterInterface */
            $router = $this->components->get('router');

            /** @var \Strider2038\ImgCache\Core\Route */
            $route = $router->getRoute($request);

            /** @var \Strider2038\ImgCache\Core\ControllerInterface */
            $controller = $route->getController();
     
            /** @var \Strider2038\ImgCache\Core\ResponseInterface */
            $response = $controller->runAction($route->getAction(), $request);
            
            $response->send();
        } catch (\Exception $ex) {
            $response = new Response\ExceptionResponse($ex);
            $response->send();
        }
    }
    
    private function getCoreComponents(): array 
    {
        return [
            'request' => function() {
                return new Core\Request();
            },
            'security' => function() {
                return new Core\Security();
            }
        ];
    }
}