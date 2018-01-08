<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Exception\ApplicationException;
use Strider2038\ImgCache\Exception\BadApiResponseException;
use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Exception\FileOperationException;
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Exception\InvalidImageException;
use Strider2038\ImgCache\Exception\InvalidMediaTypeException;
use Strider2038\ImgCache\Exception\InvalidRequestException;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Exception\InvalidRouteException;
use Strider2038\ImgCache\Exception\InvalidValueException;
use Strider2038\ImgCache\Exception\NotAllowedException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ExceptionTest extends TestCase
{
    /**
     * @test
     * @param string $class
     * @param int $code
     * @dataProvider exceptionClassAndCodeProvider
     */
    public function construct_givenExceptionClass_validCodeSet(string $class, int $code): void
    {
        /** @var ApplicationException $exception */
        $exception = new $class;

        $this->assertEquals($code, $exception->getCode());
    }

    public function exceptionClassAndCodeProvider(): array
    {
        return [
            [ApplicationException::class, HttpStatusCodeEnum::INTERNAL_SERVER_ERROR],
            [BadApiResponseException::class, HttpStatusCodeEnum::BAD_GATEWAY],
            [FileNotFoundException::class, HttpStatusCodeEnum::NOT_FOUND],
            [FileOperationException::class, HttpStatusCodeEnum::INTERNAL_SERVER_ERROR],
            [InvalidConfigurationException::class, HttpStatusCodeEnum::INTERNAL_SERVER_ERROR],
            [InvalidImageException::class, HttpStatusCodeEnum::INTERNAL_SERVER_ERROR],
            [InvalidMediaTypeException::class, HttpStatusCodeEnum::UNSUPPORTED_MEDIA_TYPE],
            [InvalidRequestException::class, HttpStatusCodeEnum::BAD_REQUEST],
            [InvalidRequestValueException::class, HttpStatusCodeEnum::BAD_REQUEST],
            [InvalidRouteException::class, HttpStatusCodeEnum::NOT_FOUND],
            [InvalidValueException::class, HttpStatusCodeEnum::INTERNAL_SERVER_ERROR],
            [NotAllowedException::class, HttpStatusCodeEnum::METHOD_NOT_ALLOWED],
        ];
    }
}
