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

use Strider2038\ImgCache\Enum\ImageProcessorTypeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemImageSource extends AbstractImageSource
{
    /** @var string */
    private $storageDirectory;
    /** @var ImageProcessorTypeEnum */
    private $processorType;

    public function __construct(
        string $cacheDirectory,
        string $storageDirectory,
        string $processorType
    ) {
        parent::__construct($cacheDirectory);
        $this->storageDirectory = $storageDirectory;
        $this->processorType = new ImageProcessorTypeEnum($processorType);
    }

    public function getStorageDirectory(): string
    {
        return $this->storageDirectory;
    }

    public function getProcessorType(): ImageProcessorTypeEnum
    {
        return $this->processorType;
    }
}
