<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service;

use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RoutingRequestHandler implements RequestHandlerInterface
{
    /** @var RequestHandlerInterface */
    private $clearAllRequestHandler;
    /** @var RequestHandlerInterface */
    private $intermediateRequestHandler;

    public function __construct(
        RequestHandlerInterface $clearAllRequestHandler,
        RequestHandlerInterface $intermediateRequestHandler
    ) {
        $this->clearAllRequestHandler = $clearAllRequestHandler;
        $this->intermediateRequestHandler = $intermediateRequestHandler;
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        $requestMethod = $request->getMethod()->getValue();
        $path = $request->getUri()->getPath();

        if ($requestMethod === HttpMethodEnum::DELETE && ($path === '/' || $path === '')) {
            $response = $this->clearAllRequestHandler->handleRequest($request);
        } else {
            $response = $this->intermediateRequestHandler->handleRequest($request);
        }

        return $response;
    }
}
