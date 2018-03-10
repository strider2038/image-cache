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
use Strider2038\ImgCache\Imaging\Storage\Driver\FilesystemStorageDriver;
use Strider2038\ImgCache\Imaging\Storage\Driver\FilesystemStorageDriverFactory;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAVStorageDriver;
use Strider2038\ImgCache\Utility\HttpClientFactoryInterface;
use Strider2038\ImgCache\Utility\HttpClientInterface;
use Strider2038\ImgCache\Utility\MetadataReaderInterface;

class FilesystemStorageDriverFactoryTest extends TestCase
{
    private const FULL_URI = 'http://example.com/';
    private const HOST_URI = 'example.com';
    private const TOKEN = 'token';

    /** @var FileOperationsInterface */
    private $fileOperations;
    /** @var HttpClientFactoryInterface */
    private $httpClientFactory;
    /** @var MetadataReaderInterface */
    private $metadataReader;

    protected function setUp(): void
    {
        $this->fileOperations = \Phake::mock(FileOperationsInterface::class);
        $this->httpClientFactory = \Phake::mock(HttpClientFactoryInterface::class);
        $this->metadataReader = \Phake::mock(MetadataReaderInterface::class);
    }

    /** @test */
    public function createFilesystemStorageDriver_noParameters_filesystemStorageDriverCreatedAndReturned(): void
    {
        $factory = $this->createFilesystemStorageDriverFactory();

        $driver = $factory->createFilesystemStorageDriver();

        $this->assertInstanceOf(FilesystemStorageDriver::class, $driver);
    }

    /** @test */
    public function createWebDAVStorageDriver_givenUriAndToken_webdavStorageDriverCreatedAndReturned(): void
    {
        $factory = $this->createFilesystemStorageDriverFactory();
        $this->givenHttpClientFactory_createClient_returnsHttpClient();

        $driver = $factory->createWebDAVStorageDriver(self::FULL_URI, self::TOKEN);

        $this->assertInstanceOf(WebDAVStorageDriver::class, $driver);

        $this->assertHttpClientFactory_createClient_isCalledOnceWithExpectedParameters();
    }

    private function createFilesystemStorageDriverFactory(): FilesystemStorageDriverFactory
    {
        return new FilesystemStorageDriverFactory(
            $this->fileOperations,
            $this->httpClientFactory,
            $this->metadataReader
        );
    }

    private function givenHttpClientFactory_createClient_returnsHttpClient(): void
    {
        $client = \Phake::mock(HttpClientInterface::class);
        \Phake::when($this->httpClientFactory)
            ->createClient(\Phake::anyParameters())
            ->thenReturn($client);
    }

    private function assertHttpClientFactory_createClient_isCalledOnceWithExpectedParameters(): void
    {
        $parameters = [
            'base_uri' => self::FULL_URI,
            'headers' => [
                'Authorization' => 'OAuth ' . self::TOKEN,
                'Host' => self::HOST_URI,
                'Accept' => '*/*',
            ],
        ];

        \Phake::verify($this->httpClientFactory, \Phake::times(1))
            ->createClient($parameters);
    }
}
