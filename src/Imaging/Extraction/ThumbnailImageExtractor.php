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
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageExtractor implements ImageExtractorInterface
{
    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    public function __construct(
        ThumbnailKeyParserInterface $keyParser,
        SourceAccessorInterface $sourceImageExtractor,
        ImageProcessorInterface $imageProcessor
    ) {
        $this->keyParser = $keyParser;
        $this->sourceAccessor = $sourceImageExtractor;
        $this->imageProcessor = $imageProcessor;
    }

    public function extract(string $key): ? Image
    {
        $thumbnailKey = $this->keyParser->parse($key);
        $sourceImage = $this->sourceAccessor->get($thumbnailKey->getPublicFilename());
        if ($sourceImage === null) {
            return null;
        }

        return $this->imageProcessor->process($sourceImage, $thumbnailKey->getProcessingConfiguration());
    }
}
