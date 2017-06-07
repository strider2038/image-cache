<?php

namespace Strider2038\ImgCache\Core;

/**
 * Description of Route
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Route 
{
    /** @var Strider2038\ImgCache\Core\ControllerInterface */
    private $controller;

    /** @var string */
    private $action;
    
    public function __construct(ControllerInterface $controller, string $action) 
    {
        $this->controller = $controller;
        $this->action = $action;
    }
    
    public function getController(): ControllerInterface 
    {
        return $this->controller;
    }
    
    public function getAction(): string 
    {
        return $this->action;
    }
}
