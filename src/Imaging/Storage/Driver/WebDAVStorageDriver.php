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

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Strider2038\ImgCache\Core\StreamFactoryInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Exception\BadApiResponseException;
use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class WebDAVStorageDriver implements FilesystemStorageDriverInterface
{
    /** @var string */
    private $baseDirectory;

    /** @var ClientInterface */
    private $client;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(string $baseDirectory, ClientInterface $client, StreamFactoryInterface $streamFactory)
    {
        $this->baseDirectory = rtrim($baseDirectory, '/') . '/';
        $this->client = $client;
        $this->streamFactory = $streamFactory;
    }

    public function getFileContents(StorageFilenameInterface $key): StreamInterface
    {
        $storageFilename = $this->baseDirectory . $key->getValue();

        try {
            $response = $this->client->request(WebDAVMethodEnum::GET, $storageFilename);
        } catch (GuzzleException $exception) {
            if ($exception->getCode() === HttpStatusCodeEnum::NOT_FOUND) {
                throw new FileNotFoundException(
                    sprintf('File "%s" not found in storage.', $storageFilename),
                    HttpStatusCodeEnum::NOT_FOUND,
                    $exception
                );
            }

            throw new BadApiResponseException(
                sprintf('Bad api response for filename "%s".', $storageFilename),
                HttpStatusCodeEnum::BAD_GATEWAY,
                $exception
            );
        }

        if ($response->getStatusCode() !== HttpStatusCodeEnum::OK) {
            throw new BadApiResponseException(
                sprintf(
                    'Unexpected response from API: %d %s.',
                    $response->getStatusCode(),
                    $response->getReasonPhrase()
                )
            );
        }

        $responseResource = $response->getBody()->detach();

        if ($responseResource === null) {
            throw new BadApiResponseException('Api response has empty body.');
        }

        return $this->streamFactory->createStreamFromResource($responseResource);
    }

    public function fileExists(StorageFilenameInterface $key): bool
    {
        // TODO: Implement fileExists() method.
    }

    public function createFile(StorageFilenameInterface $key, StreamInterface $data): void
    {
        // TODO: Implement createFile() method.
    }

    public function deleteFile(StorageFilenameInterface $key): void
    {
        // TODO: Implement deleteFile() method.
    }
}
