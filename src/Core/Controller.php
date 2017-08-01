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

use Strider2038\ImgCache\Exception\ApplicationException;
use Strider2038\ImgCache\Response\ForbiddenResponse;

/**
 * Description of Controller
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class Controller implements ControllerInterface
{
    /** @var SecurityInterface */
    protected $security;

    public function __construct(SecurityInterface $security = null)
    {
        $this->security = $security;
    }

    public function runAction(string $action, string $location): ResponseInterface
    {
        $actionName = 'action' . ucfirst($action);
        if (!method_exists($this, $actionName)) {
            throw new ApplicationException("Action '{$actionName}' does not exists");
        }
        if (!$this->isActionSafe($action)) {
            return new ForbiddenResponse();
        }
        
        return call_user_func([$this, $actionName], $location);
    }
    
    protected function getSafeActions(): array
    {
        return [];
    }
    
    private function isActionSafe(string $action): bool
    {
        if ($this->security === null || in_array($action, $this->getSafeActions())) {
            return true;
        }

        return $this->security->isAuthorized();
    }
}
