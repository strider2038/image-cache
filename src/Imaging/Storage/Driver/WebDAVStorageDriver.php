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

use Strider2038\ImgCache\Core\GuzzleClientAdapter;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
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

    /** @var GuzzleClientAdapter */
    private $clientAdapter;

    public function __construct(string $baseDirectory, GuzzleClientAdapter $clientAdapter)
    {
        $this->baseDirectory = rtrim($baseDirectory, '/') . '/';
        $this->clientAdapter = $clientAdapter;
    }

    public function getFileContents(StorageFilenameInterface $key): StreamInterface
    {
        $storageFilename = $this->baseDirectory . $key->getValue();

        $response = $this->clientAdapter->request(WebDAVMethodEnum::GET, $storageFilename);

        $statusCode = $response->getStatusCode()->getValue();

        if ($statusCode === HttpStatusCodeEnum::NOT_FOUND) {
            throw new FileNotFoundException(sprintf('File "%s" not found in storage.', $storageFilename));
        }

        if ($statusCode !== HttpStatusCodeEnum::OK) {
            throw new BadApiResponseException(
                sprintf(
                    'Unexpected response from API: %d %s.',
                    $statusCode,
                    $response->getReasonPhrase()
                )
            );
        }

        return $response->getBody();
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
