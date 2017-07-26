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

use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImage implements ExtractedImageInterface
{
    /** @var ProcessingImageInterface */
    private $processingImage;

    /** @var SaveOptions */
    private $saveOptions;

    public function __construct(
        ProcessingImageInterface $processingImage,
        SaveOptions $saveOptions
    ) {
        $this->processingImage = $processingImage;
        $this->saveOptions = $saveOptions;
    }

    public function getSaveOptions(): SaveOptions
    {
        return $this->saveOptions;
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