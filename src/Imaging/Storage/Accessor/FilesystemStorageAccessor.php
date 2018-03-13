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
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameFactoryInterface;
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
    /** @var StorageFilenameFactoryInterface */
    private $filenameFactory;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        FilesystemStorageDriverInterface $storageDriver,
        ImageFactoryInterface $imageFactory,
        StorageFilenameFactoryInterface $filenameFactory
    ) {
        $this->storageDriver = $storageDriver;
        $this->imageFactory = $imageFactory;
        $this->filenameFactory = $filenameFactory;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getImage(string $filename): Image
    {
        $storageFilename = $this->getStorageFilename($filename);
        $stream = $this->storageDriver->getFileContents($storageFilename);
        $image = $this->imageFactory->createImageFromStream($stream);

        $this->logger->info(sprintf('Image was extracted from filesystem source by key "%s".', $filename));

        return $image;
    }

    public function imageExists(string $filename): bool
    {
        $storageFilename = $this->getStorageFilename($filename);
        $exists = $this->storageDriver->fileExists($storageFilename);

        $this->logger->info(sprintf(
            'Image with key "%s" %s in filesystem source.',
            $filename,
            $exists ? 'exists' : 'does not exist'
        ));

        return $exists;
    }

    public function putImage(string $filename, Image $image): void
    {
        $storageFilename = $this->getStorageFilename($filename);
        $data = $image->getData();
        $this->storageDriver->createFile($storageFilename, $data);

        $this->logger->info(sprintf(
            "Image is successfully putted to source under key '%s'.",
            $filename
        ));
    }

    public function deleteImage(string $filename): void
    {
        $storageFilename = $this->getStorageFilename($filename);
        $this->storageDriver->deleteFile($storageFilename);

        $this->logger->info(sprintf(
            "Image with key '%s' is successfully deleted from source.",
            $filename
        ));
    }

    private function getStorageFilename(string $filename): StorageFilenameInterface
    {
        $storageFilename = $this->filenameFactory->createStorageFilename($filename);

        $this->logger->info(
            sprintf(
                'Storage filename "%s" created for filename "%s".',
                $storageFilename->getValue(),
                $filename
            )
        );

        return $storageFilename;
    }
}
