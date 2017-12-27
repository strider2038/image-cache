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

use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageExtractor implements ImageExtractorInterface
{
    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    public function __construct(
        ThumbnailKeyParserInterface $keyParser,
        StorageAccessorInterface $storageAccessor,
        ImageProcessorInterface $imageProcessor
    ) {
        $this->keyParser = $keyParser;
        $this->storageAccessor = $storageAccessor;
        $this->imageProcessor = $imageProcessor;
    }

    public function getProcessedImage(string $filename): Image
    {
        $thumbnailKey = $this->keyParser->parse($filename);
        $sourceImage = $this->storageAccessor->getImage($thumbnailKey->getPublicFilename());

        return $this->imageProcessor->process($sourceImage, $thumbnailKey->getProcessingConfiguration());
    }
}
