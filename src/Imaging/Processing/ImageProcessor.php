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

use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageProcessor implements ImageProcessorInterface
{
    /** @var ImageTransformerFactoryInterface */
    private $transformerFactory;

    public function __construct(ImageTransformerFactoryInterface $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
    }

    public function process(Image $image, ProcessingConfiguration $configuration): Image
    {
        $transformer = $this->transformerFactory->createTransformerForImage($image);

        $transformations = $configuration->getTransformations();
        foreach ($transformations as $transformation) {
            /** @var TransformationInterface $transformation */
            $transformation->apply($transformer);
        }

        $processedImage = $transformer->getImage();
        $processedImage->setSaveOptions($configuration->getSaveOptions());

        return $processedImage;
    }

    public function saveToFile(Image $image, string $filename): void
    {
        // TODO: Implement saveToFile() method.
    }
}
