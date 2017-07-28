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
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageExtractor extends SourceImageExtractor implements ImageExtractorInterface
{
    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    /** @var ThumbnailImageFactoryInterface */
    private $thumbnailImageFactory;

    public function __construct(
        ThumbnailKeyParserInterface $keyParser,
        SourceAccessorInterface $sourceAccessor,
        ProcessingConfigurationParserInterface $processingConfigurationParser,
        ThumbnailImageFactoryInterface $thumbnailImageFactory
    ) {
        parent::__construct($keyParser, $sourceAccessor);
        $this->processingConfigurationParser = $processingConfigurationParser;
        $this->thumbnailImageFactory = $thumbnailImageFactory;
    }

    public function extract(string $key): ?ImageInterface
    {
        $sourceImage = parent::extract($key);

        if ($sourceImage === null) {
            return null;
        }

        /** @var ThumbnailKeyInterface $thumbnailKey */
        $thumbnailKey = $this->keyParser->parse($key);

        $processingConfigurationRaw = $thumbnailKey->getProcessingConfiguration();

        /** @var ProcessingConfigurationInterface $processingConfiguration */
        $processingConfiguration = $this->processingConfigurationParser->parse($processingConfigurationRaw);

        return $this->thumbnailImageFactory->create($processingConfiguration, $sourceImage);
    }
}