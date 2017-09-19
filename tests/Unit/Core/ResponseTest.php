<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\DeprecatedResponse;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResponseTest extends TestCase
{
    const HEADER_TEST = 'test';
    const HEADER_VALUE = 'value';

    /**
     * @dataProvider httpCodeProvider
     */
    public function testConstruct_HttpCodeIsSet_HttpCodeReturned($httpCode): void
    {
        $response = $this->buildResponse($httpCode);
        
        $this->assertEquals($httpCode, $response->getHttpCode());
    }

    public function httpCodeProvider(): array
    {
        return [
            [200],
            [201],
            [400],
            [401],
            [403],
            [404],
            [500],
        ];
    }
    
    public function testConstruct_HttpCodeIsIncorrect_Http500Returned(): void
    {
        $response = $this->buildResponse(0);
        
        $this->assertEquals(500, $response->getHttpCode());
    }
    
    public function testConstruct_HttpVersionIs10_HttpVersion10Returned(): void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
        $response = $this->buildResponse();
        
        $this->assertEquals('1.0', $response->getHttpVersion());
    }
    
    public function testConstruct_HttpVersionIsNotSet_HttpVersion11Returned(): void
    {
        $response = $this->buildResponse();
        
        $this->assertEquals('1.1', $response->getHttpVersion());
    }
    
    /**
     * @runInSeparateProcess
     * @group separate
     */
    public function testSend_NotSent_SentOnlyOnce(): void
    {
        $response = $this->buildResponse();

        $this->assertFalse($response->isSent());
        $this->assertEquals(0, $response->testSendCount);
        $response->send();

        $this->assertTrue($response->isSent());
        $this->assertEquals(1, $response->testSendCount);

        $response->send();
        $this->assertEquals(1, $response->testSendCount);
    }

    /**
     * @runInSeparateProcess
     * @group separate
     */
    public function testSend_HeaderIsSet_HeaderIsSent(): void
    {
        $response = $this->buildResponse();
        $response->setHeader(self::HEADER_TEST, self::HEADER_VALUE);

        $response->send();

        $this->assertTrue($response->isSent());
        $headers = xdebug_get_headers();
        $this->assertContains(self::HEADER_TEST . ': ' . self::HEADER_VALUE, $headers);
    }

    private function buildResponse(int $httpCode = 200): DeprecatedResponse
    {
        $response = new class($httpCode) extends DeprecatedResponse
        {
            public $testSendCount = 0;

            public function sendContent(): void
            {
                $this->testSendCount++;
            }
        };
        return $response;
    }
}
