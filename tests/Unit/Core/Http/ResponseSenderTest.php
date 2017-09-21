<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core\Http;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\HeaderCollection;
use Strider2038\ImgCache\Core\Http\HeaderValueCollection;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\ResponseSender;
use Strider2038\ImgCache\Core\NullStream;
use Strider2038\ImgCache\Core\StringStream;
use Strider2038\ImgCache\Enum\HttpHeader;
use Strider2038\ImgCache\Enum\HttpStatusCode;

class ResponseSenderTest extends TestCase
{
    private const CONTENTS = 'contents';

    /**
     * @test
     * @runInSeparateProcess
     * @group separate
     */
    public function send_givenEmptyResponseWithHeaders_headerIsSent(): void {
        $sender = new ResponseSender();
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($response)->getStatusCode()->thenReturn(new HttpStatusCode(HttpStatusCode::OK));
        $headers = new HeaderCollection([
            HttpHeader::CONTENT_TYPE => new HeaderValueCollection([
                'text/plain'
            ]),
        ]);
        \Phake::when($response)->getHeaders()->thenReturn($headers);
        \Phake::when($response)->getBody()->thenReturn(new NullStream());

        $sender->send($response);

        $this->assertEquals(HttpStatusCode::OK, http_response_code());
        $actualHeaders = xdebug_get_headers();
        $this->assertContains('Content-type: text/plain;charset=UTF-8', $actualHeaders);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @group separate
     */
    public function send_givenStringStream_contentIsSent(): void {
        $sender = new ResponseSender();
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($response)->getStatusCode()->thenReturn(new HttpStatusCode(HttpStatusCode::OK));
        $headers = new HeaderCollection();
        \Phake::when($response)->getHeaders()->thenReturn($headers);
        $body = new StringStream(self::CONTENTS);
        \Phake::when($response)->getBody()->thenReturn($body);

        $sender->send($response);

        $this->expectOutputString(self::CONTENTS);
    }

}
