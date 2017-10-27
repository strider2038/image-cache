<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessor;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;

class ImageProcessorTest extends TestCase
{
    /** @var ImageTransformerFactoryInterface */
    private $transformerFactory;

    protected function setUp(): void
    {
        $this->transformerFactory = \Phake::mock(ImageTransformerFactoryInterface::class);
    }

    /** @test */
    public function process_givenImageAndProcessingConfiguration_processedImageIsReturned(): void
    {
        $processor = new ImageProcessor($this->transformerFactory);
        $image = \Phake::mock(Image::class);
        $processingConfiguration = \Phake::mock(ProcessingConfiguration::class);
        $transformation = $this->givenProcessingConfiguration_getTransformations_returnsCollectionWithTransformation($processingConfiguration);
        $transformer = $this->givenTransformerFactory_createTransformerForImage_returnsTransformer($image);
        $saveOptions = \Phake::mock(SaveOptions::class);
        \Phake::when($processingConfiguration)->getSaveOptions()->thenReturn($saveOptions);
        $expectedProcessedImage = $this->givenTransformer_getImage_returnsImage($transformer);

        $processedImage = $processor->process($image, $processingConfiguration);

        $this->assertSame($expectedProcessedImage, $processedImage);
        $this->assertTransformerFactory_createTransformerForImage_isCalledOnceWith($image);
        $this->assertProcessingConfiguration_getTransformations_isCalledOnce($processingConfiguration);
        $this->assertTransformation_apply_isCalledOnceWith($transformation, $transformer);
        $this->assertProcessingConfiguration_getSaveOptions_isCalledOnce($processingConfiguration);
        $this->assertImage_setSaveOptions_isCalledOnceWith($expectedProcessedImage, $saveOptions);
    }

    private function givenTransformerFactory_createTransformerForImage_returnsTransformer(Image $image): ImageTransformerInterface
    {
        $transformer = \Phake::mock(ImageTransformerInterface::class);
        \Phake::when($this->transformerFactory)->createTransformerForImage($image)->thenReturn($transformer);

        return $transformer;
    }

    private function assertTransformerFactory_createTransformerForImage_isCalledOnceWith(Image $image): void
    {
        \Phake::verify($this->transformerFactory, \Phake::times(1))->createTransformerForImage($image);
    }

    private function assertProcessingConfiguration_getTransformations_isCalledOnce(
        ProcessingConfiguration $processingConfiguration
    ): void {
        \Phake::verify($processingConfiguration, \Phake::times(1))->getTransformations();
    }

    private function assertTransformation_apply_isCalledOnceWith(
        TransformationInterface $transformation,
        ImageTransformerInterface $transformer
    ): void {
        \Phake::verify($transformation, \Phake::times(1))->apply($transformer);
    }

    private function givenProcessingConfiguration_getTransformations_returnsCollectionWithTransformation(
        ProcessingConfiguration $processingConfiguration
    ): TransformationInterface {
        $transformation = \Phake::mock(TransformationInterface::class);
        $transformations = new TransformationCollection([$transformation]);
        \Phake::when($processingConfiguration)->getTransformations()->thenReturn($transformations);

        return $transformation;
    }

    private function assertProcessingConfiguration_getSaveOptions_isCalledOnce(
        ProcessingConfiguration $processingConfiguration
    ): void {
        \Phake::verify($processingConfiguration, \Phake::times(1))->getSaveOptions();
    }

    private function givenTransformer_getImage_returnsImage(ImageTransformerInterface $transformer): Image
    {
        $expectedProcessedImage = \Phake::mock(Image::class);
        \Phake::when($transformer)->getImage()->thenReturn($expectedProcessedImage);

        return $expectedProcessedImage;
    }

    private function assertImage_setSaveOptions_isCalledOnceWith(
        Image $expectedProcessedImage,
        SaveOptions $saveOptions
    ): void {
        \Phake::verify($expectedProcessedImage, \Phake::times(1))->setSaveOptions($saveOptions);
    }
}
