<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Source;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Imaging\Source\FilesystemSource;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemSourceTest extends TestCase
{
    use FileOperationsTrait;

    private const BASE_DIRECTORY = '/base';
    private const FILENAME_NOT_EXIST = 'not.exist';
    private const FILENAME_EXISTS_FULL = self::BASE_DIRECTORY . '/cat.jpg';
    private const FILENAME_EXISTS = 'cat.jpg';
    private const DATA = 'data';
    private const CHUNK_SIZE = 8 * 1024 * 1024;

    /** @var FileOperationsInterface */
    private $fileOperations;

    public function setUp(): void
    {
        $this->fileOperations = $this->givenFileOperations();
    }

    /** @test */
    public function construct_baseDirectoryExists_baseDirectoryIsReturned(): void
    {
        $source = $this->createFilesystemSource();

        $this->assertEquals(self::BASE_DIRECTORY . '/', $source->getBaseDirectory());
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Directory .* does not exist/
     */
    public function construct_baseDirectoryDoesNotExist_exceptionThrown(): void
    {
        $this->givenFileOperations_isDirectory_returns($this->fileOperations, self::BASE_DIRECTORY, false);
        new FilesystemSource(self::BASE_DIRECTORY, $this->fileOperations);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found/
     */
    public function getFileContents_fileDoesNotExist_exceptionThrown(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_NOT_EXIST);

        $source->getFileContents($filenameKey);
    }

    /** @test */
    public function getFileContents_fileExists_fileContentsIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME_EXISTS_FULL, true);
        $expectedFileContents = $this->givenFileOperations_openFile_returnsStream(
            $this->fileOperations,
            self::FILENAME_EXISTS_FULL,
            ResourceStreamModeEnum::READ_ONLY
        );

        $fileContents = $source->getFileContents($filenameKey);

        $this->assertSame($expectedFileContents, $fileContents);
    }

    /** @test */
    public function fileExists_fileDoesNotExist_falseIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_NOT_EXIST);

        $exists = $source->fileExists($filenameKey);

        $this->assertFalse($exists);
    }

    /** @test */
    public function fileExists_fileExists_trueIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME_EXISTS_FULL, true);
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);

        $exists = $source->fileExists($filenameKey);

        $this->assertTrue($exists);
    }

    /** @test */
    public function createFile_givenKeyAndStream_directoryCreatedAndStreamIsWrittenToFile(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);
        $givenStream = $this->givenInputStream();
        $stream = $this->givenFileOperations_openFile_returnsStream(
            $this->fileOperations,
            self::FILENAME_EXISTS_FULL,
            ResourceStreamModeEnum::WRITE_AND_READ
        );

        $source->createFile($filenameKey, $givenStream);

        $this->assertFileOperations_createDirectory_isCalledOnce($this->fileOperations, self::BASE_DIRECTORY);
        \Phake::verify($stream, \Phake::times(1))->write(self::DATA);
    }

    /** @test */
    public function deleteFile_givenKey_fileIsDeleted(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);

        $source->deleteFile($filenameKey);

        $this->assertFileOperations_deleteFile_isCalledOnce($this->fileOperations, self::FILENAME_EXISTS_FULL);
    }

    private function createFilesystemSource(string $baseDirectory = self::BASE_DIRECTORY): FilesystemSource
    {
        $this->givenFileOperations_isDirectory_returns($this->fileOperations, self::BASE_DIRECTORY, true);

        return new FilesystemSource($baseDirectory, $this->fileOperations);
    }

    private function givenFilenameKey($filename): FilenameKeyInterface
    {
        $filenameKey = \Phake::mock(FilenameKeyInterface::class);

        \Phake::when($filenameKey)->getValue()->thenReturn($filename);

        return $filenameKey;
    }

    private function givenInputStream(): StreamInterface
    {
        $givenStream = \Phake::mock(StreamInterface::class);
        \Phake::when($givenStream)->eof()->thenReturn(false)->thenReturn(true);
        \Phake::when($givenStream)->read(self::CHUNK_SIZE)->thenReturn(self::DATA);

        return $givenStream;
    }
}
