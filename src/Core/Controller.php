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
use Strider2038\ImgCache\Response\ForbiddenResponse;

/**
 * Description of Controller
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class Controller extends Component implements ControllerInterface
{
    
    public function runAction(string $action, RequestInterface $request): ResponseInterface 
    {
        $actionName = 'action' . ucfirst($action);
        if (!method_exists($this, $actionName)) {
            throw new RuntimeException("Action '{$actionName}' does not exists");
        }
        if (
            !in_array($action, $this->getInsecureActions())
            && !$this->getApp()->security->isAuthorized()
        ) {
            return new ForbiddenResponse();
        }
        
        return call_user_func([$this, $actionName], $request);
    }
    
    protected function getInsecureActions(): array
    {
        return [];
    }
}
