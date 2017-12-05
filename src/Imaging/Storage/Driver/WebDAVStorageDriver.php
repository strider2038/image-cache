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
use Strider2038\ImgCache\Core\StreamFactoryInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Exception\BadApiResponseException;
use Strider2038\ImgCache\Imaging\Storage\Data\FilenameKeyInterface;

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

    public function getFileContents(FilenameKeyInterface $key): StreamInterface
    {
        $storageFilename = $this->baseDirectory . $key->getValue();

        $response = $this->client->request(WebDAVMethodEnum::GET, $storageFilename);

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

        return $this->streamFactory->createStreamFromResource($responseResource);
    }

    public function fileExists(FilenameKeyInterface $key): bool
    {
        // TODO: Implement fileExists() method.
    }

    public function createFile(FilenameKeyInterface $key, StreamInterface $data): void
    {
        // TODO: Implement createFile() method.
    }

    public function deleteFile(FilenameKeyInterface $key): void
    {
        // TODO: Implement deleteFile() method.
    }
}
