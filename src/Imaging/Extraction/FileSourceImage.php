<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Extraction;

use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Transformation\SaveOptions;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileSourceImage implements ExtractedImageInterface
{
    public function setSaveOptions(SaveOptions $saveOptions): void
    {
        // TODO: Implement setSaveOptions() method.
    }

    public function saveTo(string $filename): void
    {
        // TODO: Implement saveTo() method.
    }

    public function open(ProcessingEngineInterface $engine): ProcessingImageInterface
    {
        // TODO: Implement open() method.
    }
}