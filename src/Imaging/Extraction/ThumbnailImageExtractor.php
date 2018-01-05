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
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ProcessingConfigurationParserInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageExtractor implements ImageExtractorInterface
{
    /** @var ThumbnailFilenameParserInterface */
    private $filenameParser;

    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    public function __construct(
        ThumbnailFilenameParserInterface $filenameParser,
        ProcessingConfigurationParserInterface $processingConfigurationParser,
        StorageAccessorInterface $storageAccessor,
        ImageProcessorInterface $imageProcessor
    ) {
        $this->filenameParser = $filenameParser;
        $this->processingConfigurationParser = $processingConfigurationParser;
        $this->storageAccessor = $storageAccessor;
        $this->imageProcessor = $imageProcessor;
    }

    public function getProcessedImage(string $filename): Image
    {
        $thumbnailFilename = $this->filenameParser->getParsedFilename($filename);
        $sourceImage = $this->storageAccessor->getImage($thumbnailFilename->getValue());

        $processingConfigurationString = $thumbnailFilename->getProcessingConfiguration();
        $processingConfiguration = $this->processingConfigurationParser->parseConfiguration($processingConfigurationString);

        $transformations = $processingConfiguration->getTransformations();
        $imageParameters = $processingConfiguration->getImageParameters();

        $thumbnailImage = $this->imageProcessor->transformImage($sourceImage, $transformations);
        $thumbnailImage->setParameters($imageParameters);

        return $thumbnailImage;
    }
}
