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
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\Http\HeaderCollection;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\ResponseSender;
use Strider2038\ImgCache\Core\Streaming\NullStream;
use Strider2038\ImgCache\Core\Streaming\StringStream;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;

class ResponseSenderTest extends TestCase
{
    private const CONTENTS = 'contents';

    /**
     * @test
     * @runInSeparateProcess
     * @group separate
     */
    public function sendResponse_givenEmptyResponseWithHeaders_headerIsSent(): void {
        $sender = new ResponseSender();
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($response)->getStatusCode()->thenReturn(new HttpStatusCodeEnum(HttpStatusCodeEnum::OK));
        $headers = new HeaderCollection([
            HttpHeaderEnum::CONTENT_TYPE => new StringList(['text/plain']),
        ]);
        \Phake::when($response)->getHeaders()->thenReturn($headers);
        \Phake::when($response)->getBody()->thenReturn(new NullStream());

        $sender->sendResponse($response);

        $this->assertEquals(HttpStatusCodeEnum::OK, http_response_code());
        $actualHeaders = xdebug_get_headers();
        $this->assertContains('Content-type: text/plain;charset=UTF-8', $actualHeaders);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @group separate
     */
    public function sendResponse_givenStringStream_contentIsSent(): void {
        $sender = new ResponseSender();
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($response)->getStatusCode()->thenReturn(new HttpStatusCodeEnum(HttpStatusCodeEnum::OK));
        $headers = new HeaderCollection();
        \Phake::when($response)->getHeaders()->thenReturn($headers);
        $body = new StringStream(self::CONTENTS);
        \Phake::when($response)->getBody()->thenReturn($body);

        $sender->sendResponse($response);

        $this->expectOutputString(self::CONTENTS);
    }

}
