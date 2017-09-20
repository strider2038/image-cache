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

use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCode;
use Strider2038\ImgCache\Exception\ApplicationException;

/**
 * Description of Controller
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class Controller implements ControllerInterface
{
    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    /** @var SecurityInterface */
    protected $security;

    public function __construct(ResponseFactoryInterface $responseFactory, SecurityInterface $security = null)
    {
        $this->responseFactory = $responseFactory;
        $this->security = $security;
    }

    public function runAction(string $action, string $location): ResponseInterface
    {
        $actionName = 'action' . ucfirst($action);
        if (!method_exists($this, $actionName)) {
            throw new ApplicationException("Action '{$actionName}' does not exists");
        }
        if (!$this->isActionSafe($action)) {
            return $this->responseFactory->createMessageResponse(
                new HttpStatusCode(HttpStatusCode::FORBIDDEN)
            );
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
