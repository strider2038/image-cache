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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class HttpRequestHandler implements RequestHandlerInterface
{
    /** @var AccessControlInterface */
    private $accessControl;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var RequestHandlerInterface */
    private $concreteRequestHandler;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        AccessControlInterface $accessControl,
        ResponseFactoryInterface $responseFactory,
        RequestHandlerInterface $concreteRequestHandler
    ) {
        $this->accessControl = $accessControl;
        $this->responseFactory = $responseFactory;
        $this->concreteRequestHandler = $concreteRequestHandler;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        if ($this->accessControl->canHandleRequest($request)) {
            $response = $this->handleRequestByConcreteHandler($request);
        } else {
            $response = $this->createForbiddenResponse();
        }

        return $response;
    }

    private function handleRequestByConcreteHandler(RequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->concreteRequestHandler->handleRequest($request);
        } catch (\Throwable $exception) {
            $this->logger->error($exception);
            $response = $this->responseFactory->createExceptionResponse($exception);
        }

        return $response;
    }

    private function createForbiddenResponse(): ResponseInterface
    {
        $forbiddenStatus = new HttpStatusCodeEnum(HttpStatusCodeEnum::FORBIDDEN);

        return $this->responseFactory->createMessageResponse($forbiddenStatus);
    }
}
