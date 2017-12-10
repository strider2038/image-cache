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

    /** @var ResourceManipulatorInterface */
    private $resourceManipulator;

    /** @var ResourceManipulatorInterface */
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
    public function fileExists_givenStorageFilenameAndResourcePropertiesCollection_boolReturned(
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
        $fileContents = \Phake::mock(StreamInterface::class);

        $driver->createFile($storageFilename, $fileContents);

        $this->assertResourceManipulator_putResource_isCalledOnceWithResourceUriAndStream(
            self::FILENAME_FULL,
            $fileContents
        );
    }

    private function createWebDAVStorageDriver(): WebDAVStorageDriver
    {
        return new WebDAVStorageDriver(self::BASE_DIRECTORY, $this->resourceManipulator, $this->resourceChecker);
    }

    private function givenStorageFilename(): StorageFilenameInterface
    {
        $key = \Phake::mock(StorageFilenameInterface::class);
        \Phake::when($key)->getValue()->thenReturn(self::FILENAME);

        return $key;
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
}
