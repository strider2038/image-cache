<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing;

use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageProcessor implements ImageProcessorInterface
{
    /** @var ProcessingEngineInterface */
    private $processingEngine;

    public function __construct(ProcessingEngineInterface $processingEngine)
    {
        $this->processingEngine = $processingEngine;
    }

    public function process(ProcessingConfigurationInterface $configuration, ImageInterface $image): ProcessingImageInterface
    {
        /** @var ProcessingImageInterface $processingImage */
        $processingImage = $image->open($this->processingEngine);

        /** @var TransformationsCollection $transformations */
        $transformations = $configuration->getTransformations();

        foreach ($transformations as $transformation) {
            /** @var TransformationInterface $transformation */
            $transformation->apply($processingImage);
        }

        /** @var SaveOptions $saveOptions */
        $saveOptions = $configuration->getSaveOptions();

        $processingImage->setSaveOptions($saveOptions);

        return $processingImage;
    }

}