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
use Strider2038\ImgCache\Core\HeaderCollection;
use Strider2038\ImgCache\Core\HeaderValueCollection;
use Strider2038\ImgCache\Core\Message;
use Strider2038\ImgCache\Core\NullStream;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\HttpHeader;
use Strider2038\ImgCache\Enum\HttpProtocolVersion;

class MessageTest extends TestCase
{
    private const HEADER_VALUES = ['value1', 'value2'];
    private const HEADER_VALUES_LINE = 'value1,value2';

    /** @test */
    public function construct_nothingIsGiven_HeadersAreEmptyAndBodyIsNullStreamAndProtocolVersionIs1(): void
    {
        $message = new Message();

        $this->assertEquals('1.0', $message->getProtocolVersion());
        $this->assertCount(0, $message->getHeaders());
        $this->assertInstanceOf(NullStream::class, $message->getBody());
    }

    /** @test */
    public function setProtocolVersion_versionIs11_versionIsReturned(): void
    {
        $message = new Message();
        $protocolVersion = new HttpProtocolVersion(HttpProtocolVersion::V1_1);

        $message->setProtocolVersion($protocolVersion);

        $this->assertEquals('1.1', $message->getProtocolVersion());
    }

    /** @test */
    public function hasHeader_givenHeaderCollection_returnedIsTrue(): void
    {
        $message = new Message();
        $headers = \Phake::mock(HeaderCollection::class);
        \Phake::when($headers)->containsKey(\Phake::anyParameters())->thenReturn(true);
        $message->setHeaders($headers);

        $result = $message->hasHeader(new HttpHeader(HttpHeader::AUTHORIZATION));

        $this->assertTrue($result);
    }

    /** @test */
    public function getHeader_givenHeaderCollection_headerValueCollectionIsReturned(): void
    {
        $message = new Message();
        $headers = \Phake::mock(HeaderCollection::class);
        $headerValues = \Phake::mock(HeaderValueCollection::class);
        \Phake::when($headers)->get(\Phake::anyParameters())->thenReturn($headerValues);
        $message->setHeaders($headers);

        $actualHeaderValues = $message->getHeader(new HttpHeader(HttpHeader::AUTHORIZATION));

        $this->assertSame($headerValues, $actualHeaderValues);
    }

    /** @test */
    public function getHeaderLine_givenHeaderCollection_concatenatedHeaderValuesIsReturned(): void
    {
        $message = new Message();
        $headerValues = new HeaderValueCollection(self::HEADER_VALUES);
        $headers = new HeaderCollection([HttpHeader::AUTHORIZATION => $headerValues]);
        $message->setHeaders($headers);

        $headerLine = $message->getHeaderLine(new HttpHeader(HttpHeader::AUTHORIZATION));

        $this->assertEquals(self::HEADER_VALUES_LINE, $headerLine);
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
