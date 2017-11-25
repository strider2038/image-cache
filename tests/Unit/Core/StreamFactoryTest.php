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

use Strider2038\ImgCache\Core\ResourceStream;
use Strider2038\ImgCache\Core\StreamFactory;
use Strider2038\ImgCache\Core\StringStream;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class StreamFactoryTest extends FileTestCase
{
    private const MODE_READ_AND_WRITE = 'rb+';
    private const DATA = 'data';

    /** @test */
    public function createStreamByParameters_givenStreamDescriptorAndMode_resourceStreamCreated(): void
    {
        $factory = new StreamFactory();
        $mode = new ResourceStreamModeEnum(self::MODE_READ_AND_WRITE);

        $stream = $factory->createStreamByParameters($this->givenFile(), $mode);

        $this->assertInstanceOf(ResourceStream::class, $stream);
    }

    /** @test */
    public function createStreamFromData_givenString_stringStreamCreated(): void
    {
        $factory = new StreamFactory();

        $stream = $factory->createStreamFromData(self::DATA);

        $this->assertInstanceOf(StringStream::class, $stream);
        $this->assertEquals(self::DATA, $stream->getContents());
    }

    /** @test */
    public function createStreamFromResource_givenResource_resourceStreamCreated(): void
    {
        $factory = new StreamFactory();
        $resource = fopen($this->givenFile(), self::MODE_READ_AND_WRITE);

        $stream = $factory->createStreamFromResource($resource);

        $this->assertInstanceOf(ResourceStream::class, $stream);
    }
}
