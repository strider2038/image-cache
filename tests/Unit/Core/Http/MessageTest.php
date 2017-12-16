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
use Strider2038\ImgCache\Core\Http\Message;
use Strider2038\ImgCache\Core\Streaming\NullStream;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;
use Strider2038\ImgCache\Enum\HttpProtocolVersionEnum;

class MessageTest extends TestCase
{
    private const HEADER_VALUES = ['value1', 'value2'];
    private const HEADER_VALUES_LINE = 'value1,value2';

    /** @test */
    public function construct_nothingIsGiven_HeadersAreEmptyAndBodyIsNullStreamAndProtocolVersionIs11(): void
    {
        $message = new Message();

        $this->assertEquals('1.1', $message->getProtocolVersion());
        $this->assertCount(0, $message->getHeaders());
        $this->assertInstanceOf(NullStream::class, $message->getBody());
    }

    /** @test */
    public function setProtocolVersion_versionIs10_versionReturned(): void
    {
        $message = new Message();
        $protocolVersion = new HttpProtocolVersionEnum(HttpProtocolVersionEnum::V1_0);

        $message->setProtocolVersion($protocolVersion);

        $this->assertEquals('1.0', $message->getProtocolVersion());
    }

    /** @test */
    public function hasHeader_givenHeaderCollection_tureReturned(): void
    {
        $message = new Message();
        $headers = \Phake::mock(HeaderCollection::class);
        \Phake::when($headers)->containsKey(\Phake::anyParameters())->thenReturn(true);
        $message->setHeaders($headers);

        $result = $message->hasHeader(new HttpHeaderEnum(HttpHeaderEnum::AUTHORIZATION));

        $this->assertTrue($result);
    }

    /** @test */
    public function getHeader_givenHeaderCollection_headerValueCollectionReturned(): void
    {
        $message = new Message();
        $headers = \Phake::mock(HeaderCollection::class);
        $headerValues = \Phake::mock(StringList::class);
        \Phake::when($headers)->get(\Phake::anyParameters())->thenReturn($headerValues);
        $message->setHeaders($headers);

        $actualHeaderValues = $message->getHeader(new HttpHeaderEnum(HttpHeaderEnum::AUTHORIZATION));

        $this->assertSame($headerValues, $actualHeaderValues);
    }

    /** @test */
    public function getHeaderLine_givenHeaderCollection_concatenatedHeaderValuesReturned(): void
    {
        $message = new Message();
        $headerValues = new StringList(self::HEADER_VALUES);
        $headers = new HeaderCollection([HttpHeaderEnum::AUTHORIZATION => $headerValues]);
        $message->setHeaders($headers);

        $headerLine = $message->getHeaderLine(new HttpHeaderEnum(HttpHeaderEnum::AUTHORIZATION));

        $this->assertEquals(self::HEADER_VALUES_LINE, $headerLine);
    }

    /** @test */
    public function getHeaderLine_givenEmptyHeaderCollection_emptyHeaderValueReturned(): void
    {
        $message = new Message();
        $message->setHeaders(new HeaderCollection());

        $headerLine = $message->getHeaderLine(new HttpHeaderEnum(HttpHeaderEnum::AUTHORIZATION));

        $this->assertEquals('', $headerLine);
    }

    /** @test */
    public function getBody_givenStream_streamIsReturned(): void
    {
        $message = new Message();
        $stream = \Phake::mock(StreamInterface::class);
        $message->setBody($stream);

        $body = $message->getBody();

        $this->assertSame($stream, $body);
    }
}
