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

use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Exception\BadApiResponseException;
use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceCheckerInterface;
use Strider2038\ImgCache\Utility\GuzzleClientAdapter;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class WebDAVStorageDriver implements FilesystemStorageDriverInterface
{
    /** @var string */
    private $baseDirectory;

    /** @var GuzzleClientAdapter */
    private $clientAdapter;

    /** @var ResourceCheckerInterface */
    private $resourceChecker;

    public function __construct(
        string $baseDirectory,
        GuzzleClientAdapter $clientAdapter,
        ResourceCheckerInterface $resourceChecker
    ) {
        $this->baseDirectory = rtrim($baseDirectory, '/') . '/';
        $this->clientAdapter = $clientAdapter;
        $this->resourceChecker = $resourceChecker;
    }

    public function getFileContents(StorageFilenameInterface $filename): StreamInterface
    {
        $storageFilename = $this->baseDirectory . $filename->getValue();

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

    public function fileExists(StorageFilenameInterface $filename): bool
    {
        $storageFilename = $this->baseDirectory . $filename->getValue();

        return $this->resourceChecker->isFile($storageFilename);
    }

    public function createFile(StorageFilenameInterface $filename, StreamInterface $data): void
    {
        // TODO: Implement createFile() method.
    }

    public function deleteFile(StorageFilenameInterface $filename): void
    {
        // TODO: Implement deleteFile() method.
    }
}
