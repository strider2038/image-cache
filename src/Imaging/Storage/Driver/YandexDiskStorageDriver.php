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

use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Exception\BadApiResponseException;
use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Yandex\Disk\DiskClient;
use Yandex\Disk\Exception\DiskRequestException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexDiskStorageDriver implements FilesystemStorageDriverInterface
{
    /** @var string */
    private $baseDirectory;

    /** @var DiskClient */
    private $diskClient;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(string $baseDirectory, DiskClient $diskClient, StreamFactoryInterface $streamFactory)
    {
        $this->baseDirectory = rtrim($baseDirectory, '/') . '/';
        $this->diskClient = $diskClient;
        $this->streamFactory = $streamFactory;
    }

    public function getFileContents(StorageFilenameInterface $filename): StreamInterface
    {
        $storageFilename = $this->baseDirectory . $filename->getValue();

        try {
            $response = $this->diskClient->getFile($storageFilename);
        } catch (DiskRequestException $exception) {
            if ($exception->getCode() === HttpStatusCodeEnum::NOT_FOUND) {
                throw new FileNotFoundException(
                    sprintf('File "%s" not found.', $storageFilename),
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

        /** @var \Psr\Http\Message\StreamInterface $responseBody */
        $responseBody = $response['body'];
        $responseResource = $responseBody->detach();

        if ($responseResource === null) {
            throw new BadApiResponseException('Api response has empty body.');
        }

        return $this->streamFactory->createStreamFromResource($responseResource);
    }

    public function fileExists(StorageFilenameInterface $filename): bool
    {
        $storageFilename = $this->baseDirectory . $filename->getValue();
        $exists = false;

        try {
            $response = $this->diskClient->directoryContents($storageFilename);

            $exists = \count($response) === 1
                && array_key_exists('href', $response[0])
                && $response[0]['href'] === $storageFilename;
        } catch (DiskRequestException $exception) {
            if ($exception->getCode() !== HttpStatusCodeEnum::NOT_FOUND) {
                throw new BadApiResponseException(
                    sprintf('Bad api response for filename "%s".', $storageFilename),
                    HttpStatusCodeEnum::BAD_GATEWAY,
                    $exception
                );
            }
        }

        return $exists;
    }

    public function createFile(StorageFilenameInterface $filename, StreamInterface $data): void
    {
    }

    public function deleteFile(StorageFilenameInterface $filename): void
    {
    }
}
