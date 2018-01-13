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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationCollection;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageProcessor implements ImageProcessorInterface
{
    /** @var ImageTransformerFactoryInterface */
    private $transformerFactory;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ImageTransformerFactoryInterface $transformerFactory,
        ImageFactoryInterface $imageFactory
    ) {
        $this->transformerFactory = $transformerFactory;
        $this->imageFactory = $imageFactory;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function transformImage(Image $image, TransformationCollection $transformations): Image
    {
        $imageTransformer = $this->createImageTransformer($image);

        foreach ($transformations as $transformation) {
            /** @var TransformationInterface $transformation */
            $transformation->apply($imageTransformer);
        }

        $this->logger->info(sprintf('Transformations count applied to image: %d.', $transformations->count()));

        $data = $imageTransformer->getData();
        $parameters = $image->getParameters();

        return $this->imageFactory->createImageFromStream($data, $parameters);
    }

    public function saveImageToFile(Image $image, string $filename): void
    {
        $transformer = $this->createImageTransformer($image);
        $imageParameters = $image->getParameters();

        $transformer->setCompressionQuality($imageParameters->getQuality());
        $transformer->writeToFile($filename);

        $this->logger->info(sprintf(
            'Image was saved to file "%s" with compression quality %d.',
            $filename,
            $imageParameters->getQuality()
        ));
    }

    private function createImageTransformer(Image $image): ImageTransformerInterface
    {
        return $this->transformerFactory->createTransformer($image->getData());
    }
}
