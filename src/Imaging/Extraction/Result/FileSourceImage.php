<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Extraction\Result;

use Strider2038\ImgCache\Exception\FileOperationException;
use Strider2038\ImgCache\Imaging\Image;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileSourceImage extends Image implements ExtractedImageInterface
{
    public function saveTo(string $filename): void
    {
        $sourceFilename = $this->getFilename();

        if (!copy($sourceFilename, $filename)) {
            throw new FileOperationException("Cannot copy file '{$sourceFilename}' to '{$filename}'");
        }
    }

    public function open(ProcessingEngineInterface $engine): ProcessingImageInterface
    {
        return $engine->open($this->getFilename());
    }
}