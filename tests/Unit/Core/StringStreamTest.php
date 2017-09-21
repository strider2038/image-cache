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
use Strider2038\ImgCache\Core\StringStream;

class StringStreamTest extends TestCase
{
    private const CONTENTS = 'contents';
    private const CONTENT_SIZE = 8;

    /** @test */
    public function getContents_givenString_stringContentsIsReturned(): void
    {
        $stream = new StringStream(self::CONTENTS);

        $contents = $stream->getContents();

        $this->assertEquals(self::CONTENTS, $contents);
    }

    /** @test */
    public function toString_givenString_stringContentsIsReturned(): void
    {
        $stream = new StringStream(self::CONTENTS);

        $contents = $stream . '';

        $this->assertEquals(self::CONTENTS, $contents);
    }

    /** @test */
    public function close_givenString_nop(): void
    {
        $stream = new StringStream(self::CONTENTS);

        $stream->close();

        $this->assertEquals(self::CONTENTS, $stream);
    }

    /** @test */
    public function getSize_givenString_sizeIsReturned(): void
    {
        $stream = new StringStream(self::CONTENTS);

        $size = $stream->getSize();

        $this->assertEquals(self::CONTENT_SIZE, $size);
    }

    /** @test */
    public function isWritable_givenString_falseIsReturned(): void
    {
        $stream = new StringStream(self::CONTENTS);

        $result = $stream->isWritable();

        $this->assertFalse($result);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function write_givenString_exceptionThrown(): void
    {
        $stream = new StringStream(self::CONTENTS);

        $stream->write('');
    }

    /** @test */
    public function isReadable_givenString_trueIsReturned(): void
    {
        $stream = new StringStream(self::CONTENTS);

        $result = $stream->isReadable();

        $this->assertTrue($result);
    }

    /**
     * @test
     * @param int $offset
     * @param int $length
     * @param string $expectedString
     * @param bool $endOfFile
     * @dataProvider offsetAndLengthAndResultProvider
     */
    public function read_givenStringAndPosition_partOfStringIsReturned(
        int $offset,
        int $length,
        string $expectedString,
        bool $endOfFile
    ): void
    {
        $stream = new StringStream(self::CONTENTS);
        $stream->read($offset);

        $result = $stream->read($length);

        $this->assertEquals($expectedString, $result);
        $this->assertEquals($endOfFile, $stream->eof());
    }

    public function offsetAndLengthAndResultProvider(): array
    {
        return [
            [0, 4, 'cont', false],
            [4, 4, 'ents', true],
            [7, 4, 's', true],
            [9, 3, '', true],
            [4, -3, '', false],
        ];
    }
}
