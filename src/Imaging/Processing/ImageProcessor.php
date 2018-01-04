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

    public function process(Image $image, ProcessingConfiguration $configuration): Image
    {
        $transformer = $this->createTransformer($image);

        $transformations = $configuration->getTransformations();
        foreach ($transformations as $transformation) {
            /** @var TransformationInterface $transformation */
            $transformation->apply($transformer);
        }

        $this->logger->info(sprintf('Transformations count applied to image: %d.', $transformations->count()));

        $data = $transformer->getData();
        $saveOptions = $configuration->getSaveOptions();

        return $this->imageFactory->createImage($data, $saveOptions);
    }

    public function saveToFile(Image $image, string $filename): void
    {
        $transformer = $this->createTransformer($image);
        $saveOptions = $image->getParameters();

        $transformer->setCompressionQuality($saveOptions->getQuality());
        $transformer->writeToFile($filename);

        $this->logger->info(sprintf(
            'Image was saved to file "%s" with compression quality %d.',
            $filename,
            $saveOptions->getQuality()
        ));
    }

    private function createTransformer(Image $image): ImageTransformerInterface
    {
        return $this->transformerFactory->createTransformer($image->getData());
    }
}
