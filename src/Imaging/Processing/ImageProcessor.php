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
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageProcessor implements ImageProcessorInterface
{
    /** @var ImageTransformerFactoryInterface */
    private $transformerFactory;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    public function __construct(
        ImageTransformerFactoryInterface $transformerFactory,
        ImageFactoryInterface $imageFactory
    ) {
        $this->transformerFactory = $transformerFactory;
        $this->imageFactory = $imageFactory;
    }

    public function process(Image $image, ProcessingConfiguration $configuration): Image
    {
        $transformer = $this->createTransformer($image);

        $transformations = $configuration->getTransformations();
        foreach ($transformations as $transformation) {
            /** @var TransformationInterface $transformation */
            $transformation->apply($transformer);
        }

        $data = $transformer->getData();
        $saveOptions = $configuration->getSaveOptions();

        return $this->imageFactory->create($data, $saveOptions);
    }

    public function saveToFile(Image $image, string $filename): void
    {
        $transformer = $this->createTransformer($image);
        $saveOptions = $image->getSaveOptions();

        $transformer->setCompressionQuality($saveOptions->getQuality());
        $transformer->writeToFile($filename);
    }

    private function createTransformer(Image $image): ImageTransformerInterface
    {
        return $this->transformerFactory->createTransformer($image->getData());
    }
}
