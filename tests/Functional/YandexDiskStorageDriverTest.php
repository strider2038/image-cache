<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional;

use Strider2038\ImgCache\Core\StreamFactory;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilename;
use Strider2038\ImgCache\Imaging\Storage\Driver\YandexDiskStorageDriver;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;
use Yandex\Disk\DiskClient;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexDiskStorageDriverTest extends FunctionalTestCase
{
    private const BASE_DIRECTORY = '/imgcache/';
    private const FILENAME = 'file.json';
    private const JSON_TEMPORARY_FILENAME = self::RUNTIME_DIRECTORY . '/' . self::FILENAME;

    /** @var DiskClient */
    private $diskClient;

    /** @var YandexDiskStorageDriver */
    private $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $token = getenv('YANDEX_DISK_ACCESS_TOKEN');
        $this->diskClient = new DiskClient($token);
        $this->diskClient->delete(self::BASE_DIRECTORY);
        $this->diskClient->createDirectory(self::BASE_DIRECTORY);

        $this->driver = new YandexDiskStorageDriver(
            self::BASE_DIRECTORY,
            $this->diskClient,
            new StreamFactory()
        );
    }

    /** @test */
    public function getFileContents_givenExistingFilename_streamWithFileContentsReturned(): void
    {
        $this->givenJsonFile(self::JSON_TEMPORARY_FILENAME);
        $this->diskClient->uploadFile(self::BASE_DIRECTORY, [
            'path' => self::JSON_TEMPORARY_FILENAME,
            'name' => self::FILENAME,
            'size' => filesize(self::JSON_TEMPORARY_FILENAME),
        ]);
        $key = new StorageFilename(self::FILENAME);

        $stream = $this->driver->getFileContents($key);

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertStringEqualsFile(self::JSON_TEMPORARY_FILENAME, $stream->getContents());
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     */
    public function getFileContents_givenNotExistingFilename_exceptionThrown(): void
    {
        $key = new StorageFilename(self::FILENAME);

        $this->driver->getFileContents($key);
    }

    /** @test */
    public function fileExists_givenExistingFilename_trueReturned(): void
    {
        $this->givenJsonFile(self::JSON_TEMPORARY_FILENAME);
        $this->diskClient->uploadFile(self::BASE_DIRECTORY, [
            'path' => self::JSON_TEMPORARY_FILENAME,
            'name' => self::FILENAME,
            'size' => filesize(self::JSON_TEMPORARY_FILENAME),
        ]);
        $key = new StorageFilename(self::FILENAME);

        $fileExists = $this->driver->fileExists($key);

        $this->assertTrue($fileExists);
    }

    /** @test */
    public function fileExists_givenNotExistingFilename_falseReturned(): void
    {
        $key = new StorageFilename(self::FILENAME);

        $fileExists = $this->driver->fileExists($key);

        $this->assertFalse($fileExists);
    }
}
