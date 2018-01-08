<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Data;

use Strider2038\ImgCache\Imaging\Naming\DirectoryNameInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class StorageFilenameFactory implements StorageFilenameFactoryInterface
{
    /** @var DirectoryNameInterface */
    private $rootDirectory;

    public function __construct(DirectoryNameInterface $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function createStorageFilename(string $filename): StorageFilenameInterface
    {
        return new StorageFilename($this->rootDirectory . $filename);
    }
}
