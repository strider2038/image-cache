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
use Strider2038\ImgCache\Core\Message;
use Strider2038\ImgCache\Core\NullStream;
use Strider2038\ImgCache\Enum\HttpProtocolVersion;

class MessageTest extends TestCase
{
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
    public function getBody_givenStream_streamIsReturned(): void
    {

    }
}
