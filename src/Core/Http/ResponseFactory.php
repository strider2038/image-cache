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

use Strider2038\ImgCache\Core\ReadOnlyResourceStream;
use Strider2038\ImgCache\Core\StringStream;
use Strider2038\ImgCache\Enum\HttpHeader;
use Strider2038\ImgCache\Enum\HttpStatusCode;
use Strider2038\ImgCache\Exception\FileNotFoundException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResponseFactory implements ResponseFactoryInterface
{
    /** @var RequestInterface */
    private $request;

    /** @var bool */
    private $isDebugged;

    public function __construct(RequestInterface $request, bool $isDebugged = false)
    {
        $this->request = $request;
        $this->isDebugged = $isDebugged;
    }

    public function createMessageResponse(HttpStatusCode $code, string $message = ''): ResponseInterface
    {
        $response = new Response($code);
        $response->setBody(new StringStream($message));
        $response->setProtocolVersion($this->request->getProtocolVersion());

        return $response;
    }

    public function createExceptionResponse(\Throwable $exception): ResponseInterface
    {
        $code = $exception->getCode();
        if (HttpStatusCode::isValid($code) && $code >= 400 && $code < 600) {
            $httpStatusCode = new HttpStatusCode($code);
        } else {
            $httpStatusCode = new HttpStatusCode(HttpStatusCode::INTERNAL_SERVER_ERROR);
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

    public function createFileResponse(HttpStatusCode $code, string $filename): ResponseInterface
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException(sprintf("File '%s' not found", $filename));
        }

        $response = new Response($code);
        $response->setBody(new ReadOnlyResourceStream($filename));
        $response->setProtocolVersion($this->request->getProtocolVersion());

        $headers = new HeaderCollection();
        $headers->set(new HttpHeader(HttpHeader::CONTENT_TYPE), new HeaderValueCollection([
            mime_content_type($filename)
        ]));

        $response->setHeaders($headers);

        return $response;
    }
}