<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Response;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Exception\ApplicationException;
use Strider2038\ImgCache\Response\ExceptionResponse;

class ExceptionResponseTest extends TestCase
{
    const EXCEPTION_MESSAGE = 'message';
    const EXCEPTION_CODE = 404;

    public function testConstruct_GivenApplicationExceptionWithHttpCode_AppropriateHttpCodeIsReturned(): void
    {
        $exception = new ApplicationException(self::EXCEPTION_MESSAGE, self::EXCEPTION_CODE);

        $response = new ExceptionResponse($exception);

        $this->assertEquals(self::EXCEPTION_CODE, $response->getHttpCode());
    }

    public function testGetMessage_GivenExceptionAndIsDebugFalse_MessageIsEmptyComposed(): void
    {
        $exception = new \Exception();
        $response = new ExceptionResponse($exception, false);

        $message = $response->getMessage();

        $this->assertEquals(500, $response->getHttpCode());
        $this->assertEquals('', $message);
    }

    public function testGetMessage_GivenExceptionAndIsDebugTrue_MessageIsComposed(): void
    {
        $exception = new \Exception(self::EXCEPTION_MESSAGE);
        $response = new ExceptionResponse($exception, true);

        $message = $response->getMessage();

        $this->assertEquals(500, $response->getHttpCode());
        $this->assertStringStartsWith('Application exception', $message);
    }
}
