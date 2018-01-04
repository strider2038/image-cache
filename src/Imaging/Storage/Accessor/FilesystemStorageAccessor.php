<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Accessor;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\FilenameKeyMapperInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\FilesystemStorageDriverInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemStorageAccessor implements StorageAccessorInterface
{
    /** @var FilesystemStorageDriverInterface */
    private $storageDriver;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var FilenameKeyMapperInterface */
    private $keyMapper;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        FilesystemStorageDriverInterface $storageDriver,
        ImageFactoryInterface $imageFactory,
        FilenameKeyMapperInterface $keyMapper
    ) {
        $this->storageDriver = $storageDriver;
        $this->imageFactory = $imageFactory;
        $this->keyMapper = $keyMapper;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getImage(string $key): Image
    {
        $filenameKey = $this->composeFilenameKey($key);
        $stream = $this->storageDriver->getFileContents($filenameKey);
        $image = $this->imageFactory->createImageFromStream($stream);

        $this->logger->info(sprintf('Image was extracted from filesystem source by key "%s"', $key));

        return $image;
    }

    public function imageExists(string $key): bool
    {
        $filenameKey = $this->composeFilenameKey($key);
        $exists = $this->storageDriver->fileExists($filenameKey);

        $this->logger->info(sprintf(
            'Image with key "%s" %s in filesystem source',
            $key,
            $exists ? 'exists' : 'does not exist'
        ));

        return $exists;
    }

    public function putImage(string $key, Image $image): void
    {
        $filenameKey = $this->composeFilenameKey($key);
        $data = $image->getData();
        $this->storageDriver->createFile($filenameKey, $data);

        $this->logger->info(sprintf(
            "Image is successfully putted to source under key '%s'",
            $key
        ));
    }

    public function deleteImage(string $key): void
    {
        $filenameKey = $this->composeFilenameKey($key);
        $this->storageDriver->deleteFile($filenameKey);

        $this->logger->info(sprintf(
            "Image with key '%s' is successfully deleted from source",
            $key
        ));
    }

    private function composeFilenameKey(string $key): StorageFilenameInterface
    {
        $filenameKey = $this->keyMapper->getKey($key);

        $this->logger->info(sprintf('Key "%s" is mapped to "%s"', $key, $filenameKey->getValue()));

        return $filenameKey;
    }
}
