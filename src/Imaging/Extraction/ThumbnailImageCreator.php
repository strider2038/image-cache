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
use Strider2038\ImgCache\Imaging\Parsing\Processing\ProcessingConfigurationParserInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageCreator implements ThumbnailImageCreatorInterface
{
    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    public function __construct(
        ProcessingConfigurationParserInterface $processingConfigurationParser,
        ImageProcessorInterface $imageProcessor
    ) {
        $this->processingConfigurationParser = $processingConfigurationParser;
        $this->imageProcessor = $imageProcessor;
    }

    public function createThumbnailImageByConfiguration(Image $image, string $processingConfiguration): Image
    {
        $parsedConfiguration = $this->processingConfigurationParser->parseConfiguration($processingConfiguration);

        $transformations = $parsedConfiguration->getTransformations();
        $imageParameters = $parsedConfiguration->getImageParameters();

        $thumbnailImage = $this->imageProcessor->transformImage($image, $transformations);
        $thumbnailImage->setParameters($imageParameters);

        return $thumbnailImage;
    }
}
