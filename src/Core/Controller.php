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
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;

/**
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
        if (!$this->isActionSafe($action)) {
            return $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::FORBIDDEN)
            );
        }

        $action = $this->createAction($action, $location);

        return $action->run();
    }
    
    protected function getSafeActions(): array
    {
        return [];
    }

    abstract protected function createAction(string $action, string $location): ActionInterface;
    
    private function isActionSafe(string $action): bool
    {
        if ($this->security === null || in_array($action, $this->getSafeActions(), true)) {
            return true;
        }

        return $this->security->isAuthorized();
    }
}
