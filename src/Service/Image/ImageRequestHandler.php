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

use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageRequestHandler implements RequestHandlerInterface
{
    /** @var ImageRequestHandlerFactoryInterface */
    private $requestHandlerFactory;

    public function __construct(ImageRequestHandlerFactoryInterface $requestHandlerFactory)
    {
        $this->requestHandlerFactory = $requestHandlerFactory;
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $concreteRequestHandler = $this->requestHandlerFactory->createRequestHandlerByHttpMethod($method);

        return $concreteRequestHandler->handleRequest($request);
    }
}
