<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Driver;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\FilesystemStorageDriver;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemStorageDriverTest extends TestCase
{
    use FileOperationsTrait;

    private const BASE_DIRECTORY = '/base/';
    private const BASE_DIRECTORY_WITHOUT_TRAILING_SLASH = '/base';
    private const FILENAME_NOT_EXIST = 'not.exist';
    private const FILENAME_EXISTS_FULL = self::BASE_DIRECTORY . 'cat.jpg';
    private const FILENAME_EXISTS = 'cat.jpg';
    private const DATA = 'data';
    private const CHUNK_SIZE = 8 * 1024 * 1024;

    /** @var DirectoryNameInterface */
    private $baseDirectory;

    /** @var FileOperationsInterface */
    private $fileOperations;

    public function setUp(): void
    {
        $this->baseDirectory = $this->givenDirectoryName();
        $this->fileOperations = $this->givenFileOperations();
    }

    /** @test */
    public function getFileContents_fileExists_fileContentsIsReturned(): void
    {
        $driver = $this->createFilesystemStorageDriver();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME_EXISTS_FULL, true);
        $expectedFileContents = $this->givenFileOperations_openFile_returnsStream($this->fileOperations);

        $fileContents = $driver->getFileContents($filenameKey);

        $this->assertFileOperations_openFile_isCalledOnceWithFilenameAndMode(
            $this->fileOperations,
            self::FILENAME_EXISTS_FULL,
            ResourceStreamModeEnum::READ_ONLY
        );
        $this->assertSame($expectedFileContents, $fileContents);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found/
     */
    public function getFileContents_fileDoesNotExists_exceptionThrown(): void
    {
        $driver = $this->createFilesystemStorageDriver();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_NOT_EXIST);
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME_NOT_EXIST, false);

        $driver->getFileContents($filenameKey);
    }

    /** @test */
    public function fileExists_fileDoesNotExist_falseIsReturned(): void
    {
        $driver = $this->createFilesystemStorageDriver();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_NOT_EXIST);

        $exists = $driver->fileExists($filenameKey);

        $this->assertFalse($exists);
    }

    /** @test */
    public function fileExists_fileExists_trueIsReturned(): void
    {
        $driver = $this->createFilesystemStorageDriver();
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME_EXISTS_FULL, true);
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);

        $exists = $driver->fileExists($filenameKey);

        $this->assertTrue($exists);
    }

    /** @test */
    public function createFile_givenKeyAndStream_directoryCreatedAndStreamIsWrittenToFile(): void
    {
        $driver = $this->createFilesystemStorageDriver();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);
        $givenStream = $this->givenInputStream();
        $stream = $this->givenFileOperations_openFile_returnsStream($this->fileOperations);

        $driver->createFile($filenameKey, $givenStream);

        $this->assertFileOperations_createDirectory_isCalledOnce(
            $this->fileOperations,
            self::BASE_DIRECTORY_WITHOUT_TRAILING_SLASH
        );
        $this->assertFileOperations_openFile_isCalledOnceWithFilenameAndMode(
            $this->fileOperations,
            self::FILENAME_EXISTS_FULL,
            ResourceStreamModeEnum::WRITE_AND_READ
        );
        \Phake::verify($stream, \Phake::times(1))->write(self::DATA);
    }

    /** @test */
    public function deleteFile_givenKey_fileIsDeleted(): void
    {
        $driver = $this->createFilesystemStorageDriver();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);

        $driver->deleteFile($filenameKey);

        $this->assertFileOperations_deleteFile_isCalledOnce($this->fileOperations, self::FILENAME_EXISTS_FULL);
    }

    private function createFilesystemStorageDriver(): FilesystemStorageDriver
    {
        $this->givenFileOperations_isDirectory_returns($this->fileOperations, self::BASE_DIRECTORY, true);

        return new FilesystemStorageDriver($this->baseDirectory, $this->fileOperations);
    }

    private function givenDirectoryName(): DirectoryNameInterface
    {
        $directoryName = \Phake::mock(DirectoryNameInterface::class);
        \Phake::when($directoryName)->__toString()->thenReturn(self::BASE_DIRECTORY);

        return $directoryName;
    }

    private function givenFilenameKey($filename): StorageFilenameInterface
    {
        $filenameKey = \Phake::mock(StorageFilenameInterface::class);

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
