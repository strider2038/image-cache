<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration\ImageSource;

use Strider2038\ImgCache\Imaging\Naming\DirectoryName;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemImageSource extends AbstractImageSource
{
    /** @var DirectoryNameInterface */
    private $storageDirectory;

    /** @var string */
    private $processorType;

    public function __construct(
        string $cacheDirectory,
        string $storageDirectory,
        string $processorType
    ) {
        parent::__construct($cacheDirectory);
        $storageDirectory = $storageDirectory === '' ? '' : rtrim($storageDirectory, '/') . '/';
        $this->storageDirectory = new DirectoryName($storageDirectory);
        $this->processorType = $processorType;
    }

    public function getStorageDirectory(): DirectoryNameInterface
    {
        return $this->storageDirectory;
    }

    public function getProcessorType(): string
    {
        return $this->processorType;
    }
}
