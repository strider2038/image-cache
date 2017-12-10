<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Integration;

use GuzzleHttp\ClientInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilename;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAVStorageDriver;
use Strider2038\ImgCache\Tests\Support\IntegrationTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class WebDAVStorageDriverTest extends IntegrationTestCase
{
    private const BASE_DIRECTORY = '/imgcache/';
    private const FILENAME = 'file.json';
    private const JSON_TEMPORARY_FILENAME = self::RUNTIME_DIRECTORY . '/' . self::FILENAME;

    /** @var ClientInterface */
    private $client;

    /** @var WebDAVStorageDriver */
    private $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $container = $this->loadContainer('webdav-storage-driver.yml');
        $tokenHeader = 'OAuth ' . getenv('YANDEX_DISK_ACCESS_TOKEN');
        $container->setParameter('token', $tokenHeader);
        $container->setParameter('storage.directory', self::BASE_DIRECTORY);

        $this->driver = $container->get('webdav_storage_driver');

        $this->client = $container->get('client');
        $this->client->request(WebDAVMethodEnum::DELETE, self::BASE_DIRECTORY);
        $this->client->request(WebDAVMethodEnum::MKCOL, self::BASE_DIRECTORY);
    }

    /** @test */
    public function getFileContents_givenExistingFilename_streamWithFileContentsReturned(): void
    {
        $this->givenJsonFile(self::JSON_TEMPORARY_FILENAME);
        $this->givenUploadedFile(
            self::BASE_DIRECTORY . self::FILENAME,
            self::JSON_TEMPORARY_FILENAME
        );
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
        $this->givenUploadedFile(
            self::BASE_DIRECTORY . self::FILENAME,
            self::JSON_TEMPORARY_FILENAME
        );
        $key = new StorageFilename(self::FILENAME);

        $fileExists = $this->driver->fileExists($key);

        $this->assertTrue($fileExists);
    }

    /** @test */
    public function fileExists_givenNotExistingFilename_false(): void
    {
        $key = new StorageFilename(self::FILENAME);

        $fileExists = $this->driver->fileExists($key);

        $this->assertFalse($fileExists);
    }

    private function givenUploadedFile(string $remoteFilename, string $localFilename): void
    {
        $finfo = finfo_open(FILEINFO_MIME);
        $mime = finfo_file($finfo, $localFilename);
        $mimeParts = explode(';', $mime);

        $this->client->request(
            WebDAVMethodEnum::PUT,
            $remoteFilename,
            [
                'headers' => [
                    'Content-Type' => $mimeParts[0],
                    'Content-Length' => filesize($localFilename),
                    'Etag' => md5_file($localFilename),
                    'Sha256' => hash_file('sha256', $localFilename),
                ],
                'body' => fopen($localFilename, 'rb'),
                'expect' => true,
            ]
        );
    }
}
