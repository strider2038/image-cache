<?php

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Exception\RuntimeException;

/**
 * Description of Controller
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Controller
{
    
    public function runAction(string $action, RequestInterface $request): Response 
    {
        $actionName = 'action' . ucfirst($action);
        if (!method_exists($this, $actionName)) {
            throw new RuntimeException("Action '{$actionName}' does not exists");
        }
        
        return call_user_func([$this, $actionName], $request);
    }
}
