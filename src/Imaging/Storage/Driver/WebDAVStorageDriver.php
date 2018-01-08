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
    /** @var ResourceManipulatorInterface */
    private $resourceManipulator;

    /** @var ResourceCheckerInterface */
    private $resourceChecker;

    public function __construct(
        ResourceManipulatorInterface $clientAdapter,
        ResourceCheckerInterface $resourceChecker
    ) {
        $this->resourceManipulator = $clientAdapter;
        $this->resourceChecker = $resourceChecker;
    }

    public function getFileContents(StorageFilenameInterface $filename): StreamInterface
    {
        return $this->resourceManipulator->getResource($filename);
    }

    public function fileExists(StorageFilenameInterface $filename): bool
    {
        return $this->resourceChecker->isFile($filename);
    }

    public function createFile(StorageFilenameInterface $filename, StreamInterface $data): void
    {
        $directories = array_values(
            array_filter(
                explode('/', pathinfo($filename, PATHINFO_DIRNAME))
            )
        );

        if (\count($directories) > 0 && $directories[0] !== '.') {
            $this->createDirectoriesRecursively($directories);
        }

        $this->resourceManipulator->putResource($filename, $data);
    }

    public function deleteFile(StorageFilenameInterface $filename): void
    {
        $this->resourceManipulator->deleteResource($filename);
    }

    private function createDirectoriesRecursively(array $directories): void
    {
        $directoryName = '';

        foreach ($directories as $directory) {
            $directoryName .= '/' . $directory;
            if (!$this->resourceChecker->isDirectory($directoryName)) {
                $this->resourceManipulator->createDirectory($directoryName);
            }
        }
    }
}
