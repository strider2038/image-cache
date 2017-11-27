<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core\Http;

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\ReadOnlyResourceStream;
use Strider2038\ImgCache\Core\StreamFactoryInterface;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResponseFactory implements ResponseFactoryInterface
{
    /** @var RequestInterface */
    private $request;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var bool */
    private $isDebugged;

    public function __construct(
        RequestInterface $request,
        StreamFactoryInterface $streamFactory,
        FileOperationsInterface $fileOperations,
        bool $isDebugged = false
    ) {
        $this->request = $request;
        $this->streamFactory = $streamFactory;
        $this->fileOperations = $fileOperations;
        $this->isDebugged = $isDebugged;
    }

    public function createMessageResponse(HttpStatusCodeEnum $code, string $message = ''): ResponseInterface
    {
        $response = new Response($code);
        $bodyStream = $this->streamFactory->createStreamFromData($message);
        $response->setBody($bodyStream);
        $response->setProtocolVersion($this->request->getProtocolVersion());

        return $response;
    }

    public function createExceptionResponse(\Throwable $exception): ResponseInterface
    {
        $code = $exception->getCode();

        if (HttpStatusCodeEnum::isValid($code)) {
            $httpStatusCode = new HttpStatusCodeEnum($code);
        } else {
            $httpStatusCode = new HttpStatusCodeEnum(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR);
        }

        if (!$this->isDebugged) {
            $message = $exception->getMessage();
        } else {
            $message = sprintf(
                "Application exception #%d '%s' in file: %s (%d)\n\nStack trace:\n%s\n",
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            );
        }

        return $this->createMessageResponse($httpStatusCode, $message);
    }

    public function createFileResponse(HttpStatusCodeEnum $code, string $filename): ResponseInterface
    {
        $response = new Response($code);
        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::WRITE_AND_READ);
        $bodyStream = $this->fileOperations->openFile($filename, $mode);
        $response->setBody($bodyStream);
        $response->setProtocolVersion($this->request->getProtocolVersion());

        $headers = new HeaderCollection();
        $headers->set(new HttpHeaderEnum(HttpHeaderEnum::CONTENT_TYPE), new HeaderValueCollection([
            mime_content_type($filename)
        ]));

        $response->setHeaders($headers);

        return $response;
    }
}
