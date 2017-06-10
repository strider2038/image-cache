<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Response;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResponseTest extends TestCase
{
    /**
     * @dataProvider httpCodeProvider
     */
    public function testConstruct_HttpCodeIsSet_HttpCodeReturned($httpCode): void
    {
        $request = new class($httpCode) extends Response {
            protected function sendContent(): void {}
        };
        
        $this->assertEquals($httpCode, $request->getHttpCode());
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
        $request = new class(23523) extends Response {
            protected function sendContent(): void {}
        };
        
        $this->assertEquals(500, $request->getHttpCode());
    }
    
    public function testConstruct_HttpVersionIs10_HttpVersion10Returned(): void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
        $request = new class(200) extends Response {
            protected function sendContent(): void {}
        };
        
        $this->assertEquals('1.0', $request->getHttpVersion());
    }
    
    public function testConstruct_HttpVersionIsNotSet_HttpVersion11Returned(): void
    {
        $request = new class(200) extends Response {
            protected function sendContent(): void {}
        };
        
        $this->assertEquals('1.1', $request->getHttpVersion());
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testSend_NotSend_IsSent(): void
    {
        $request = new class(200) extends Response {
            protected function sendContent(): void {}
        };
        
        $this->assertFalse($request->isSent());
        $request->send();
        $this->assertTrue($request->isSent());
    }
}
