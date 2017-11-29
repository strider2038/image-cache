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
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ResourceStreamTest extends FileTestCase
{
    private const MODE_READ_ONLY = 'rb';
    private const MODE_READ_AND_WRITE = 'rb+';
    private const MODE_WRITE_ONLY = 'wb';
    private const MODE_WRITE_AND_READ = 'wb+';
    private const MODE_APPEND_ONLY = 'ab';
    private const MODE_APPEND_AND_READ = 'ab+';
    private const MODE_WRITE_IF_NOT_EXIST = 'xb';
    private const MODE_WRITE_AND_READ_IF_NOT_EXIST = 'xb+';
    private const MODE_WRITE_WITHOUT_TRUNCATE = 'cb';
    private const MODE_WRITE_AND_READ_WITHOUT_TRUNCATE = 'cb+';
    private const FILENAME_WRITE = self::TEST_CACHE_DIR . '/file_write.json';
    private const CONTENTS = 'contents';

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidValueException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Invalid resource descriptor
     */
    public function construct_givenInvalidResource_exceptionThrown(): void
    {
        new ResourceStream('');
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidValueException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Unsupported resource mode
     */
    public function construct_givenResourceWithUnsupportedMode_exceptionThrown(): void
    {
        $resource = fopen($this->givenFile(), 'r');

        new ResourceStream($resource);
    }

    /**
     * @test
     * @param string $filename
     * @param string $mode
     * @param bool $isReadable
     * @param bool $isWritable
     * @dataProvider filenameAndStreamModesProvider
     */
    public function construct_givenJsonFileAndMode_streamIsCreatedWithExpectedReadWriteMode(
        ?string $filename,
        string $mode,
        bool $isReadable,
        bool $isWritable
    ): void {
        $filename = $filename ?? $this->givenFile();
        $stream = $this->createResourceStream($filename, new ResourceStreamModeEnum($mode));

        $this->assertEquals($isReadable, $stream->isReadable());
        $this->assertEquals($isWritable, $stream->isWritable());
    }

    public function filenameAndStreamModesProvider(): array
    {
        return [
            [null, self::MODE_READ_ONLY, true, false],
            [null, self::MODE_READ_AND_WRITE, true, true],
            [null, self::MODE_WRITE_ONLY, false, true],
            [null, self::MODE_WRITE_AND_READ, true, true],
            [null, self::MODE_APPEND_ONLY, false, true],
            [null, self::MODE_APPEND_AND_READ, true, true],
            [self::FILENAME_WRITE, self::MODE_WRITE_IF_NOT_EXIST, false, true],
            [self::FILENAME_WRITE, self::MODE_WRITE_AND_READ_IF_NOT_EXIST, true, true],
            [null, self::MODE_WRITE_WITHOUT_TRUNCATE, false, true],
            [null, self::MODE_WRITE_AND_READ_WITHOUT_TRUNCATE, true, true],
        ];
    }

    /** @test */
    public function getContents_givenJsonFile_jsonFileContentsIsReturned(): void
    {
        $stream = $this->createResourceStream();

        $contents = $stream->getContents();

        $this->assertEquals(self::FILE_JSON_CONTENTS, $contents);
    }

    /** @test */
    public function toString_givenJsonFile_jsonFileContentsIsReturned(): void
    {
        $stream = $this->createResourceStream();

        $contents = $stream . '';

        $this->assertEquals(self::FILE_JSON_CONTENTS, $contents);
    }

    /** @test */
    public function close_givenJsonFile_streamIsNotReadableAndWritable(): void
    {
        $stream = $this->createResourceStream();

        $stream->close();

        $this->assertFalse($stream->isReadable());
        $this->assertFalse($stream->isWritable());
    }

    /** @test */
    public function getSize_givenJsonFile_jsonFileSizeIsReturned(): void
    {
        $filename = $this->givenFile();
        $stream = $this->createResourceStream($filename);

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
        $stream = $this->createResourceStream();
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
    public function write_givenFile_contentIsWrittenToFile(): void
    {
        $stream = $this->createResourceStream(self::FILENAME_WRITE, self::MODE_WRITE_ONLY);

        $count = $stream->write(self::CONTENTS);
        $stream->close();

        $this->assertStringEqualsFile(self::FILENAME_WRITE, self::CONTENTS);
        $this->assertEquals(8, $count);
    }

    /** @test */
    public function read_givenJsonFile_partOfJsonFileContentsIsReturned(): void
    {
        $stream = $this->createResourceStream();

        $contents = $stream->read(4);

        $this->assertEquals(substr(self::FILE_JSON_CONTENTS, 0, 4), $contents);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Resource is write only
     */
    public function read_givenWriteOnlyStream_exceptionThrown(): void
    {
        $stream = $this->createResourceStream(self::FILENAME_WRITE, self::MODE_WRITE_ONLY);

        $stream->read(1);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Resource is read only
     */
    public function write_givenReadOnlyStream_exceptionThrown(): void
    {
        $stream = $this->createResourceStream($this->givenFile(), self::MODE_READ_ONLY);

        $stream->write(self::CONTENTS);
    }

    /** @test */
    public function rewind_givenReadJsonFile_jsonFileContentsReturned(): void
    {
        $stream = $this->createResourceStream();
        $stream->getContents();

        $stream->rewind();

        $contents = $stream->getContents();
        $this->assertEquals(self::FILE_JSON_CONTENTS, $contents);
    }

    private function createResourceStream(
        string $filename = null,
        string $mode = self::MODE_READ_AND_WRITE
    ): ResourceStream {
        $filename = $filename ?? $this->givenFile();
        $resource = fopen($filename, $mode);

        return new ResourceStream($resource);
    }
}
