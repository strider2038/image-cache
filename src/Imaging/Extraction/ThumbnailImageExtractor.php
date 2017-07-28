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

use Strider2038\ImgCache\Imaging\Extraction\Request\FileExtractionRequestInterface;
use Strider2038\ImgCache\Imaging\Extraction\Request\ThumbnailRequestConfigurationInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ProcessingConfigurationParserInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;
use Strider2038\ImgCache\Imaging\Source\FilesystemSourceInterface;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;
use Strider2038\ImgCache\Imaging\Source\Mapping\FilenameKeyMapperInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageExtractor implements ImageExtractorInterface
{
    /** @var FilesystemSourceInterface */
    private $source;

    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var FilenameKeyMapperInterface */
    private $keyMapper;

    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    /** @var ThumbnailImageFactoryInterface */
    private $thumbnailImageFactory;

    public function __construct(
        FilesystemSourceInterface $source,
        ThumbnailKeyParserInterface $keyParser,
        FilenameKeyMapperInterface $keyMapper,
        ProcessingConfigurationParserInterface $processingConfigurationParser,
        ThumbnailImageFactoryInterface $thumbnailImageFactory
    ) {
        $this->source = $source;
        $this->keyParser = $keyParser;
        $this->keyMapper = $keyMapper;
        $this->processingConfigurationParser = $processingConfigurationParser;
        $this->thumbnailImageFactory = $thumbnailImageFactory;
    }

    public function extract(string $key): ?ImageInterface
    {
        /** @var ThumbnailKeyInterface $thumbnailKey */
        $thumbnailKey = $this->keyParser->parse($key);

        $sourceFilename = $thumbnailKey->getSourceFilename();

        /** @var FilenameKeyInterface $sourceKey */
        $sourceKey = $this->keyMapper->getKey($sourceFilename);

        /** @var ImageInterface $sourceImage */
        $sourceImage = $this->source->get($sourceKey);

        if ($sourceImage === null) {
            return null;
        }

        $processingConfigurationRaw = $thumbnailKey->getProcessingConfiguration();

        /** @var ProcessingConfigurationInterface $processingConfiguration */
        $processingConfiguration = $this->processingConfigurationParser->parse($processingConfigurationRaw);

        return $this->thumbnailImageFactory->create($processingConfiguration, $sourceImage);
    }

    public function exists(string $key): bool
    {
        /** @var ThumbnailRequestConfigurationInterface $requestConfiguration */
        $requestConfiguration = $this->keyParser->getRequestConfiguration($key);

        /** @var FileExtractionRequestInterface $extractionRequest */
        $extractionRequest = $requestConfiguration->getExtractionRequest();

        return $this->source->exists($extractionRequest);
    }

}