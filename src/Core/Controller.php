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

use Strider2038\ImgCache\Core\Http\RequestInterface;
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

    /** @var ActionFactoryInterface */
    protected $actionFactory;

    /** @var SecurityInterface */
    protected $security;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ActionFactoryInterface $actionFactory,
        SecurityInterface $security = null
    ) {
        $this->responseFactory = $responseFactory;
        $this->actionFactory = $actionFactory;
        $this->security = $security ?? new NullSecurity();
    }

    public function runAction(string $actionId, RequestInterface $request): ResponseInterface
    {
        if (!$this->isActionSafe($actionId)) {
            return $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::FORBIDDEN)
            );
        }

        $action = $this->actionFactory->createAction($actionId);

        return $action->processRequest($request);
    }
    
    protected function getSafeActionIds(): array
    {
        return [];
    }

    private function isActionSafe(string $actionId): bool
    {
        if (\in_array($actionId, $this->getSafeActionIds(), true)) {
            return true;
        }

        return $this->security->isAuthorized();
    }
}
