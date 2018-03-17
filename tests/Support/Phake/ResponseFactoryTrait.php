<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support\Phake;

use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
trait ResponseFactoryTrait
{
    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    protected function givenResponseFactory(): void
    {
        $this->responseFactory = \Phake::mock(ResponseFactoryInterface::class);
    }

    protected function givenResponseFactory_createMessageResponse_returnsResponseWithCode(int $code): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);

        \Phake::when($response)
            ->getStatusCode()
            ->thenReturn(new HttpStatusCodeEnum($code));

        \Phake::when($this->responseFactory)
            ->createMessageResponse(\Phake::anyParameters())
            ->thenReturn($response);

        return $response;
    }

    protected function givenResponseFactory_createFileResponse_returnsResponse(): void
    {
        $response = \Phake::mock(ResponseInterface::class);

        \Phake::when($response)
            ->getStatusCode()
            ->thenReturn(new HttpStatusCodeEnum(HttpStatusCodeEnum::OK));

        \Phake::when($this->responseFactory)
            ->createFileResponse(\Phake::anyParameters())
            ->thenReturn($response);
    }

    protected function assertResponseFactory_createMessageResponse_isCalledOnceWithCode(int $code): void
    {
        \Phake::verify($this->responseFactory, \Phake::times(1))
            ->createMessageResponse(\Phake::capture($httpStatusCode), \Phake::capture($message));

        /** @var HttpStatusCodeEnum $httpStatusCode */
        $this->assertEquals($code, $httpStatusCode->getValue());
    }

    protected function assertResponseFactory_createFileResponse_isCalledOnceWith(
        int $expectedCode,
        string $expectedFilename
    ): void {
        \Phake::verify($this->responseFactory, \Phake::times(1))
            ->createFileResponse(\Phake::capture($httpStatusCode), \Phake::capture($filename));

        /** @var HttpStatusCodeEnum $httpStatusCode */
        $this->assertEquals($expectedCode, $httpStatusCode->getValue());
        $this->assertEquals($expectedFilename, $filename);
    }

    abstract public static function assertEquals($expected, $actual, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false);
}
