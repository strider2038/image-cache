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

use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ProcessingConfigurationParserInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;
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

    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    public function __construct(
        ThumbnailKeyParserInterface $keyParser,
        SourceAccessorInterface $sourceImageExtractor,
        ProcessingConfigurationParserInterface $processingConfigurationParser,
        ImageProcessorInterface $imageProcessor
    ) {
        $this->keyParser = $keyParser;
        $this->sourceAccessor = $sourceImageExtractor;
        $this->processingConfigurationParser = $processingConfigurationParser;
        $this->imageProcessor = $imageProcessor;
    }

    public function extract(string $key): ? ImageInterface
    {
        /** @var ThumbnailKeyInterface $thumbnailKey */
        $thumbnailKey = $this->keyParser->parse($key);

        $publicFilename = $thumbnailKey->getPublicFilename();

        $sourceImage = $this->sourceAccessor->get($publicFilename);

        if ($sourceImage === null) {
            return null;
        }

        $configurationString = $thumbnailKey->getProcessingConfiguration();

        /** @var ProcessingConfigurationInterface $processingConfiguration */
        // @todo move method and object to ThumbnailKeyParser
        $processingConfiguration = $this->processingConfigurationParser->parse($configurationString);

        return $this->imageProcessor->process($processingConfiguration, $sourceImage);
    }


}
