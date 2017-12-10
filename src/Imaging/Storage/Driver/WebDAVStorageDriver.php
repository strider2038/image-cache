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
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceCheckerInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceManipulatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class WebDAVStorageDriver implements FilesystemStorageDriverInterface
{
    /** @var string */
    private $baseDirectory;

    /** @var ResourceManipulatorInterface */
    private $resourceManipulator;

    /** @var ResourceCheckerInterface */
    private $resourceChecker;

    public function __construct(
        string $baseDirectory,
        ResourceManipulatorInterface $clientAdapter,
        ResourceCheckerInterface $resourceChecker
    ) {
        $this->baseDirectory = rtrim($baseDirectory, '/') . '/';
        $this->resourceManipulator = $clientAdapter;
        $this->resourceChecker = $resourceChecker;
    }

    public function getFileContents(StorageFilenameInterface $filename): StreamInterface
    {
        $storageFilename = $this->baseDirectory . $filename->getValue();

        return $this->resourceManipulator->getResource($storageFilename);
    }

    public function fileExists(StorageFilenameInterface $filename): bool
    {
        $storageFilename = $this->baseDirectory . $filename->getValue();

        return $this->resourceChecker->isFile($storageFilename);
    }

    public function createFile(StorageFilenameInterface $filename, StreamInterface $data): void
    {
        $storageFilename = $this->baseDirectory . $filename->getValue();

        $this->resourceManipulator->putResource($storageFilename, $data);
    }

    public function deleteFile(StorageFilenameInterface $filename): void
    {
        // TODO: Implement deleteFile() method.
    }
}
