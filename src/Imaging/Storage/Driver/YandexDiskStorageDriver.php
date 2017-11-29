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

use Strider2038\ImgCache\Core\StreamFactoryInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Exception\BadApiResponse;
use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Imaging\Storage\Data\FilenameKeyInterface;
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

    public function getFileContents(FilenameKeyInterface $key): StreamInterface
    {
        $storageFilename = $this->baseDirectory . $key->getValue();

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

            throw new BadApiResponse(
                sprintf('Bad api response for filename "%s".', $storageFilename),
                HttpStatusCodeEnum::BAD_GATEWAY,
                $exception
            );
        }

        /** @var \Psr\Http\Message\StreamInterface $responseBody */
        $responseBody = $response['body'];
        $responseResource = $responseBody->detach();

        return $this->streamFactory->createStreamFromResource($responseResource);
    }

    public function fileExists(FilenameKeyInterface $key): bool
    {
    }

    public function createFile(FilenameKeyInterface $key, StreamInterface $data): void
    {
    }

    public function deleteFile(FilenameKeyInterface $key): void
    {
    }
}
