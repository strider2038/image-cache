<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Driver;

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\RequestOptionsFactory;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceChecker;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceManipulator;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourcePropertiesGetter;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\XmlResponseParser;
use Strider2038\ImgCache\Utility\HttpClientFactoryInterface;
use Strider2038\ImgCache\Utility\HttpClientInterface;
use Strider2038\ImgCache\Utility\MetadataReaderInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemStorageDriverFactory
{
    /** @var FileOperationsInterface */
    private $fileOperations;
    /** @var MetadataReaderInterface */
    private $metadataReader;
    /** @var HttpClientFactoryInterface */
    private $httpClientFactory;

    public function __construct(
        FileOperationsInterface $fileOperations,
        HttpClientFactoryInterface $httpClientFactory,
        MetadataReaderInterface $metadataReader
    ) {
        $this->fileOperations = $fileOperations;
        $this->httpClientFactory = $httpClientFactory;
        $this->metadataReader = $metadataReader;
    }

    public function createFilesystemStorageDriver(): FilesystemStorageDriver
    {
        return new FilesystemStorageDriver($this->fileOperations);
    }

    public function createWebDAVStorageDriver(string $uri, string $oauthToken): WebDAVStorageDriver
    {
        $httpClient = $this->createHttpClientWithBaseUriAndOAuthToken($uri, $oauthToken);

        return new WebDAVStorageDriver(
            new ResourceManipulator(
                $httpClient,
                new RequestOptionsFactory(
                    $this->metadataReader
                )
            ),
            new ResourceChecker(
                new ResourcePropertiesGetter(
                    $httpClient,
                    new XmlResponseParser()
                )
            )
        );
    }

    private function createHttpClientWithBaseUriAndOAuthToken(string $uri, string $oauthToken): HttpClientInterface
    {
        return $this->httpClientFactory->createClient([
            'base_uri' => $uri,
            'headers' => [
                'Authorization' => 'OAuth ' . $oauthToken,
                'Host' => parse_url($uri, PHP_URL_HOST),
                'Accept' => '*/*',
            ],
        ]);
    }
}
