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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageExtractor implements ImageExtractorInterface
{
    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var SourceImageExtractor */
    private $sourceImageExtractor;

    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    /** @var ImageProcessorInterface */
    private $thumbnailImageFactory;

    public function __construct(
        ThumbnailKeyParserInterface $keyParser,
        SourceImageExtractor $sourceImageExtractor,
        ProcessingConfigurationParserInterface $processingConfigurationParser,
        ImageProcessorInterface $thumbnailImageFactory
    ) {
        $this->keyParser = $keyParser;
        $this->sourceImageExtractor = $sourceImageExtractor;
        $this->processingConfigurationParser = $processingConfigurationParser;
        $this->thumbnailImageFactory = $thumbnailImageFactory;
    }

    public function extract(string $key): ?ImageInterface
    {
        $sourceImage = $this->sourceImageExtractor->extract($key);

        if ($sourceImage === null) {
            return null;
        }

        /** @var ThumbnailKeyInterface $thumbnailKey */
        $thumbnailKey = $this->keyParser->parse($key);

        $unparsedConfiguration = $thumbnailKey->getProcessingConfiguration();

        /** @var ProcessingConfigurationInterface $processingConfiguration */
        $processingConfiguration = $this->processingConfigurationParser->parse($unparsedConfiguration);

        return $this->thumbnailImageFactory->process($processingConfiguration, $sourceImage);
    }

    public function exists(string $key): bool
    {
        return $this->sourceImageExtractor->exists($key);
    }
}