<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Exception\RuntimeException;

/**
 * Description of Controller
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class Controller implements ControllerInterface
{
    
    public function runAction(string $action, RequestInterface $request): ResponseInterface 
    {
        $actionName = 'action' . ucfirst($action);
        if (!method_exists($this, $actionName)) {
            throw new RuntimeException("Action '{$actionName}' does not exists");
        }
        
        return call_user_func([$this, $actionName], $request);
    }
}
