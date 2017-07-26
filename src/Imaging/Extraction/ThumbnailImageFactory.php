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

use Strider2038\ImgCache\Imaging\Extraction\Request\ThumbnailRequestConfigurationInterface;
use Strider2038\ImgCache\Imaging\Extraction\Result\ExtractedImageInterface;
use Strider2038\ImgCache\Imaging\Extraction\Result\ThumbnailImage;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageFactory implements ThumbnailImageFactoryInterface
{
    /** @var ProcessingEngineInterface */
    private $processingEngine;

    public function __construct(ProcessingEngineInterface $processingEngine)
    {
        $this->processingEngine = $processingEngine;
    }

    public function create(
        ThumbnailRequestConfigurationInterface $requestConfiguration,
        ExtractedImageInterface $image
    ): ThumbnailImage {
        /** @var ProcessingImageInterface $processingImage */
        $processingImage = $image->open($this->processingEngine);

        /** @var TransformationsCollection $transformations */
        $transformations = $requestConfiguration->getTransformations();

        /** @var SaveOptions $saveOptions */
        $saveOptions = $requestConfiguration->getSaveOptions();

        foreach ($transformations as $transformation) {
            /** @var TransformationInterface $transformation */
            $transformation->apply($processingImage);
        }

        return new ThumbnailImage($processingImage, $saveOptions);
    }

}