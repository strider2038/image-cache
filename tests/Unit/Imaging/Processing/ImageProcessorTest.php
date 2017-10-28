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
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessor;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;

class ImageProcessorTest extends TestCase
{
    private const FILENAME = 'filename';
    private const QUALITY = 75;

    /** @var ImageTransformerFactoryInterface */
    private $transformerFactory;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    protected function setUp(): void
    {
        $this->transformerFactory = \Phake::mock(ImageTransformerFactoryInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
    }

    /** @test */
    public function process_givenImageAndProcessingConfiguration_processedImageIsReturned(): void
    {
        $processor = $this->createImageProcessor();
        $image = $this->givenImage();
        $stream = $this->givenImage_getData_returnsStream($image);
        $processingConfiguration = \Phake::mock(ProcessingConfiguration::class);
        $transformation = $this->givenProcessingConfiguration_getTransformations_returnsCollectionWithTransformation($processingConfiguration);
        $transformer = $this->givenTransformerFactory_createTransformer_returnsTransformer($stream);
        $saveOptions = $this->givenProcessingConfiguration_getSaveOptions_returnsSaveOptions($processingConfiguration);
        $expectedProcessedImage = $this->givenImageFactory_create_returnsImage();
        $processedStream = $this->givenTransformer_getData_returnsStream($transformer);

        $processedImage = $processor->process($image, $processingConfiguration);

        $this->assertSame($expectedProcessedImage, $processedImage);
        $this->assertImage_getData_isCalledOnce($image);
        $this->assertTransformerFactory_createTransformer_isCalledOnceWith($stream);
        $this->assertProcessingConfiguration_getTransformations_isCalledOnce($processingConfiguration);
        $this->assertTransformation_apply_isCalledOnceWith($transformation, $transformer);
        $this->assertProcessingConfiguration_getSaveOptions_isCalledOnce($processingConfiguration);
        $this->assertImageFactory_create_isCalledOnceWith($processedStream, $saveOptions);
    }

    /** @test */
    public function saveToFile_givenImageAndFilename_imageIsSaved(): void
    {
        $processor = $this->createImageProcessor();
        $image = $this->givenImage();
        $stream = $this->givenImage_getData_returnsStream($image);
        $transformer = $this->givenTransformerFactory_createTransformer_returnsTransformer($stream);
        $this->givenImageHasSaveOptionsWithQuality($image, self::QUALITY);

        $processor->saveToFile($image, self::FILENAME);

        $this->assertImage_getData_isCalledOnce($image);
        $this->assertTransformerFactory_createTransformer_isCalledOnceWith($stream);
        $this->assertImage_getSaveOptions_isCalledOnce($image);
        $this->assertTransformer_setCompressionQuality_isCalledOnceWith($transformer, self::QUALITY);
        $this->assertTransformer_writeToFile_isCalledOnceWith($transformer, self::FILENAME);
    }

    private function givenTransformerFactory_createTransformer_returnsTransformer(
        StreamInterface $stream
    ): ImageTransformerInterface {
        $transformer = \Phake::mock(ImageTransformerInterface::class);
        \Phake::when($this->transformerFactory)->createTransformer($stream)->thenReturn($transformer);
        \Phake::when($transformer)->setCompressionQuality(\Phake::anyParameters())->thenReturn($transformer);
        \Phake::when($transformer)->writeToFile(\Phake::anyParameters())->thenReturn($transformer);

        return $transformer;
    }

    private function assertTransformerFactory_createTransformer_isCalledOnceWith(StreamInterface $stream): void
    {
        \Phake::verify($this->transformerFactory, \Phake::times(1))->createTransformer($stream);
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

    private function givenTransformer_getData_returnsStream(ImageTransformerInterface $transformer): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($transformer)->getData()->thenReturn($stream);

        return $stream;
    }

    private function givenImage(): Image
    {
        return \Phake::mock(Image::class);
    }

    private function createImageProcessor(): ImageProcessor
    {
        return new ImageProcessor($this->transformerFactory, $this->imageFactory);
    }

    private function givenProcessingConfiguration_getSaveOptions_returnsSaveOptions(
        ProcessingConfiguration $processingConfiguration
    ): SaveOptions {
        $saveOptions = \Phake::mock(SaveOptions::class);
        \Phake::when($processingConfiguration)->getSaveOptions()->thenReturn($saveOptions);

        return $saveOptions;
    }

    private function assertImage_getSaveOptions_isCalledOnce(Image $image): void
    {
        \Phake::verify($image, \Phake::times(1))->getSaveOptions();
    }

    private function assertTransformer_setCompressionQuality_isCalledOnceWith(
        ImageTransformerInterface $transformer,
        int $quality
    ): void {
        \Phake::verify($transformer, \Phake::times(1))->setCompressionQuality($quality);
    }

    private function givenImageHasSaveOptionsWithQuality(Image $image, int $quality): void
    {
        $saveOptions = \Phake::mock(SaveOptions::class);
        \Phake::when($image)->getSaveOptions()->thenReturn($saveOptions);
        \Phake::when($saveOptions)->getQuality()->thenReturn($quality);
    }

    private function assertTransformer_writeToFile_isCalledOnceWith(
        ImageTransformerInterface $transformer,
        string $filename
    ): void {
        \Phake::verify($transformer, \Phake::times(1))->writeToFile($filename);
    }

    private function givenImage_getData_returnsStream(Image $image): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($image)->getData()->thenReturn($stream);

        return $stream;
    }

    private function assertImage_getData_isCalledOnce(Image $image): void
    {
        \Phake::verify($image, \Phake::times(1))->getData();
    }

    private function givenImageFactory_create_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->create(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }

    private function assertImageFactory_create_isCalledOnceWith(
        StreamInterface $stream,
        SaveOptions $saveOptions
    ): void {
        \Phake::verify($this->imageFactory, \Phake::times(1))->create($stream, $saveOptions);
    }
}
