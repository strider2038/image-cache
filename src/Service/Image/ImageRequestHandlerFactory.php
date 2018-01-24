<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service\Image;

use Strider2038\ImgCache\Core\ActionInterface;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\NotAllowedRequestHandler;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Exception\InvalidRouteException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageRequestHandlerFactory implements ImageRequestHandlerFactoryInterface
{
    /** @var ActionInterface */
    private $getAction;
    /** @var ActionInterface */
    private $createAction;
    /** @var ActionInterface */
    private $replaceAction;
    /** @var ActionInterface */
    private $deleteAction;

    public function __construct(
        GetImageHandlerAction $getAction,
        CreateImageHandler $createAction = null,
        ReplaceImageHandler $replaceAction = null,
        DeleteImageHandler $deleteAction = null
    ) {
        $this->getAction = $getAction;
        $this->createAction = $createAction ?? new NotAllowedRequestHandler();
        $this->replaceAction = $replaceAction ?? new NotAllowedRequestHandler();
        $this->deleteAction = $deleteAction ?? new NotAllowedRequestHandler();
    }

    public function createRequestHandlerByHttpMethod(HttpMethodEnum $method): RequestHandlerInterface
    {
        $httpMethod = $method->getValue();
        $map = $this->getActionsMap();

        if (array_key_exists($httpMethod, $map)) {
            $handler = $map[$httpMethod];
        } else {
            throw new InvalidRouteException(sprintf('Handler for http method "%s" not found', $httpMethod));
        }

        return $handler;
    }

    private function getActionsMap(): array
    {
        return [
            HttpMethodEnum::GET => $this->getAction,
            HttpMethodEnum::POST => $this->createAction,
            HttpMethodEnum::PUT => $this->replaceAction,
            HttpMethodEnum::DELETE => $this->deleteAction
        ];
    }
}
