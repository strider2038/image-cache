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
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceCheckerInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceManipulatorInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAVStorageDriver;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class WebDAVStorageDriverTest extends TestCase
{
    use ProviderTrait;

    private const BASE_DIRECTORY = 'base_directory';
    private const FILENAME = 'filename.jpg';
    private const FILENAME_FULL = self::BASE_DIRECTORY . '/' . self::FILENAME;
    private const FILENAME_IN_SUBDIRECTORY = 'a/b/c/' . self::FILENAME;
    private const FILENAME_IN_SUBDIRECTORY_FULL = self::BASE_DIRECTORY . '/' . self::FILENAME_IN_SUBDIRECTORY;
    private const SUBDIRECTORY_A = self::BASE_DIRECTORY . '/a';
    private const SUBDIRECTORY_B = self::BASE_DIRECTORY . '/a/b';
    private const SUBDIRECTORY_C = self::BASE_DIRECTORY . '/a/b/c';

    /** @var ResourceManipulatorInterface */
    private $resourceManipulator;

    /** @var ResourceCheckerInterface */
    private $resourceChecker;

    protected function setUp(): void
    {
        $this->resourceManipulator = \Phake::mock(ResourceManipulatorInterface::class);
        $this->resourceChecker = \Phake::mock(ResourceCheckerInterface::class);
    }

    /** @test */
    public function getFileContents_givenExistingStorageFilename_streamReturned(): void
    {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();
        $stream = $this->givenResourceManipulator_getResource_returnsStream();

        $fileContents = $driver->getFileContents($storageFilename);

        $this->assertResourceManipulator_getResource_isCalledOnceWithResourceUri(self::FILENAME_FULL);
        $this->assertInstanceOf(StreamInterface::class, $fileContents);
        $this->assertSame($stream, $fileContents);
    }

    /**
     * @test
     * @dataProvider boolValuesProvider
     * @param bool $expectedFileExists
     */
    public function fileExists_givenStorageFilenameAndResourceCheckerReturnsIsFile_boolReturned(
        bool $expectedFileExists
    ): void {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();
        $this->givenResourceChecker_isFile_returnsBool($expectedFileExists);

        $fileExists = $driver->fileExists($storageFilename);

        $this->assertResourceChecker_isFile_isCalledOnceWithResourceUri(self::FILENAME_FULL);
        $this->assertEquals($expectedFileExists, $fileExists);
    }

    /** @test */
    public function createFile_givenStorageFilenameAndFileContents_fileInStorageCreated(): void
    {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();
        $fileContents = $this->givenStream();

        $driver->createFile($storageFilename, $fileContents);

        $this->assertResourceManipulator_putResource_isCalledOnceWithResourceUriAndStream(
            self::FILENAME_FULL,
            $fileContents
        );
        $this->assertResourceChecker_isDirectory_isNeverCalled();
    }

    /** @test */
    public function createFile_givenStorageFilenameInSubDirectoryAndFileContents_directoriesCreatedRecursively(): void
    {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename(self::FILENAME_IN_SUBDIRECTORY);
        $fileContents = $this->givenStream();
        $this->givenResourceChecker_isDirectory_returnsBool(false);

        $driver->createFile($storageFilename, $fileContents);

        $this->assertResourceChecker_isDirectory_isCalledOnceWithDirectoryName(self::SUBDIRECTORY_A);
        $this->assertResourceChecker_isDirectory_isCalledOnceWithDirectoryName(self::SUBDIRECTORY_B);
        $this->assertResourceChecker_isDirectory_isCalledOnceWithDirectoryName(self::SUBDIRECTORY_C);
        $this->assertResourceManipulator_createDirectory_isCalledOnceWithDirectoryUri(self::SUBDIRECTORY_A);
        $this->assertResourceManipulator_createDirectory_isCalledOnceWithDirectoryUri(self::SUBDIRECTORY_B);
        $this->assertResourceManipulator_createDirectory_isCalledOnceWithDirectoryUri(self::SUBDIRECTORY_C);
        $this->assertResourceManipulator_putResource_isCalledOnceWithResourceUriAndStream(
            self::FILENAME_IN_SUBDIRECTORY_FULL,
            $fileContents
        );
    }

    /** @test */
    public function deleteFile_givenStorageFilename_resourceManipulatorDeleteResourceIsCalled(): void
    {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();

        $driver->deleteFile($storageFilename);

        $this->assertResourceManipulator_deleteResource_isCalledOnceWithResourceUri(self::FILENAME_FULL);
    }

    private function createWebDAVStorageDriver(): WebDAVStorageDriver
    {
        return new WebDAVStorageDriver(
            self::BASE_DIRECTORY,
            $this->resourceManipulator,
            $this->resourceChecker
        );
    }

    private function givenStorageFilename(string $filename = self::FILENAME): StorageFilenameInterface
    {
        $key = \Phake::mock(StorageFilenameInterface::class);
        \Phake::when($key)->getValue()->thenReturn($filename);

        return $key;
    }

    private function givenStream(): StreamInterface
    {
        return \Phake::mock(StreamInterface::class);
    }

    private function assertResourceChecker_isFile_isCalledOnceWithResourceUri(string $storageFilename): void
    {
        \Phake::verify($this->resourceChecker, \Phake::times(1))->isFile($storageFilename);
    }

    private function givenResourceChecker_isFile_returnsBool(bool $isFile): void
    {
        \Phake::when($this->resourceChecker)->isFile(\Phake::anyParameters())->thenReturn($isFile);
    }

    private function assertResourceManipulator_getResource_isCalledOnceWithResourceUri(string $resourceUri): void
    {
        \Phake::verify($this->resourceManipulator, \Phake::times(1))->getResource($resourceUri);
    }

    private function givenResourceManipulator_getResource_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->resourceManipulator)->getResource(\Phake::anyParameters())->thenReturn($stream);

        return $stream;
    }

    private function assertResourceManipulator_putResource_isCalledOnceWithResourceUriAndStream(
        string $resourceUri,
        StreamInterface $fileContents
    ): void {
        \Phake::verify($this->resourceManipulator, \Phake::times(1))->putResource($resourceUri, $fileContents);
    }

    private function assertResourceManipulator_deleteResource_isCalledOnceWithResourceUri(string $resourceUri): void
    {
        \Phake::verify($this->resourceManipulator, \Phake::times(1))->deleteResource($resourceUri);
    }

    private function assertResourceManipulator_createDirectory_isCalledOnceWithDirectoryUri(string $directoryUri): void
    {
        \Phake::verify($this->resourceManipulator, \Phake::times(1))->createDirectory($directoryUri);
    }

    private function assertResourceChecker_isDirectory_isCalledOnceWithDirectoryName(string $directoryName): void
    {
        \Phake::verify($this->resourceChecker, \Phake::times(1))->isDirectory($directoryName);
    }

    private function assertResourceChecker_isDirectory_isNeverCalled(): void
    {
        \Phake::verify($this->resourceChecker, \Phake::times(0))->isDirectory(\Phake::anyParameters());
    }

    private function givenResourceChecker_isDirectory_returnsBool(bool $isDirectory): void
    {
        \Phake::when($this->resourceChecker)->isDirectory(\Phake::anyParameters())->thenReturn($isDirectory);
    }
}
