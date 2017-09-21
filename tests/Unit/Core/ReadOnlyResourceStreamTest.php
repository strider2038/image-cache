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

use Strider2038\ImgCache\Core\ReadOnlyResourceStream;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ReadOnlyResourceStreamTest extends FileTestCase
{
    /** @test */
    public function getContents_givenJsonFile_jsonFileContentsIsReturned(): void
    {
        $stream = $this->createReadOnlyResourceStream();

        $contents = $stream->getContents();

        $this->assertEquals(self::FILE_JSON_CONTENTS, $contents);
    }

    /** @test */
    public function toString_givenJsonFile_jsonFileContentsIsReturned(): void
    {
        $stream = $this->createReadOnlyResourceStream();

        $contents = $stream . '';

        $this->assertEquals(self::FILE_JSON_CONTENTS, $contents);
    }

    /** @test */
    public function getSize_givenJsonFile_jsonFileSizeIsReturned(): void
    {
        $filename = $this->givenFile();
        $stream = new ReadOnlyResourceStream($filename);

        $size = $stream->getSize();

        $this->assertEquals(filesize($filename), $size);
    }

    /**
     * @test
     * @param int $readLength
     * @param bool $expectedIsEndOfStream
     * @dataProvider readLengthAndEndOfStreamProvider
     */
    public function eof_givenJsonFile_boolIsReturned(int $readLength, bool $expectedIsEndOfStream): void
    {
        $stream = $this->createReadOnlyResourceStream();
        $stream->read($readLength);

        $isEndOfStream = $stream->eof();

        $this->assertEquals($expectedIsEndOfStream, $isEndOfStream);
    }

    public function readLengthAndEndOfStreamProvider(): array
    {
        return [
            [1, false],
            [1000, true],
        ];
    }

    /** @test */
    public function isWritable_givenJsonFile_falseIsReturned(): void
    {
        $stream = $this->createReadOnlyResourceStream();

        $result = $stream->isWritable();

        $this->assertFalse($result);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function write_givenJsonFile_exceptionThrown(): void
    {
        $stream = $this->createReadOnlyResourceStream();

        $stream->write('');
    }

    /** @test */
    public function isReadable_givenJsonFileAndResourceIsNotClosed_trueIsReturned(): void
    {
        $stream = $this->createReadOnlyResourceStream();

        $result = $stream->isReadable();

        $this->assertTrue($result);
    }
    /** @test */
    public function isReadable_givenJsonFileAndResourceIsClosed_falseIsReturned(): void
    {
        $stream = $this->createReadOnlyResourceStream();
        $stream->close();

        $result = $stream->isReadable();

        $this->assertFalse($result);
    }

    /** @test */
    public function read_givenJsonFile_partOfJsonFileContentsIsReturned(): void
    {
        $stream = $this->createReadOnlyResourceStream();

        $contents = $stream->read(4);

        $this->assertEquals(substr(self::FILE_JSON_CONTENTS, 0, 4), $contents);
    }

    private function createReadOnlyResourceStream(): ReadOnlyResourceStream
    {
        $stream = new ReadOnlyResourceStream($this->givenFile());

        return $stream;
    }
}
