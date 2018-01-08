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
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessor;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;

class ImageProcessorTest extends TestCase
{
    use LoggerTrait;

    private const FILENAME = 'filename';
    private const QUALITY = 75;

    /** @var ImageTransformerFactoryInterface */
    private $transformerFactory;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->transformerFactory = \Phake::mock(ImageTransformerFactoryInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function transformImage_givenImageAndTransformations_transformedImageReturned(): void
    {
        $processor = $this->createImageProcessor();
        $image = $this->givenImage();
        $stream = $this->givenImage_getData_returnsStream($image);
        $imageParameters = $this->givenImage_getParameters_returnsImageParameters($image);
        $transformation = \Phake::mock(TransformationInterface::class);
        $transformations = new TransformationCollection([$transformation]);
        $transformer = $this->givenTransformerFactory_createTransformer_returnsTransformer($stream);
        $expectedTransformedImage = $this->givenImageFactory_createImageFromStream_returnsImage();
        $processedStream = $this->givenTransformer_getData_returnsStream($transformer);

        $transformedImage = $processor->transformImage($image, $transformations);

        $this->assertSame($expectedTransformedImage, $transformedImage);
        $this->assertImage_getData_isCalledOnce($image);
        $this->assertImage_getParameters_isCalledOnce($image);
        $this->assertTransformerFactory_createTransformer_isCalledOnceWithStream($stream);
        $this->assertTransformation_apply_isCalledOnceWith($transformation, $transformer);
        $this->assertImageFactory_createImageFromStream_isCalledOnceWithStreamAndImageParameters($processedStream, $imageParameters);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /** @test */
    public function saveToFile_givenImageAndFilename_imageIsSaved(): void
    {
        $processor = $this->createImageProcessor();
        $image = $this->givenImage();
        $stream = $this->givenImage_getData_returnsStream($image);
        $transformer = $this->givenTransformerFactory_createTransformer_returnsTransformer($stream);
        $this->givenImageHasParametersWithQuality($image, self::QUALITY);

        $processor->saveImageToFile($image, self::FILENAME);

        $this->assertImage_getData_isCalledOnce($image);
        $this->assertTransformerFactory_createTransformer_isCalledOnceWithStream($stream);
        $this->assertImage_getParameters_isCalledOnce($image);
        $this->assertTransformer_setCompressionQuality_isCalledOnceWith($transformer, self::QUALITY);
        $this->assertTransformer_writeToFile_isCalledOnceWith($transformer, self::FILENAME);
        $this->assertLogger_info_isCalledOnce($this->logger);
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

    private function assertTransformerFactory_createTransformer_isCalledOnceWithStream(StreamInterface $stream): void
    {
        \Phake::verify($this->transformerFactory, \Phake::times(1))->createTransformer($stream);
    }

    private function assertTransformation_apply_isCalledOnceWith(
        TransformationInterface $transformation,
        ImageTransformerInterface $transformer
    ): void {
        \Phake::verify($transformation, \Phake::times(1))->apply($transformer);
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
        $imageProcessor = new ImageProcessor($this->transformerFactory, $this->imageFactory);
        $imageProcessor->setLogger($this->logger);

        return $imageProcessor;
    }

    private function assertImage_getParameters_isCalledOnce(Image $image): void
    {
        \Phake::verify($image, \Phake::times(1))->getParameters();
    }

    private function assertTransformer_setCompressionQuality_isCalledOnceWith(
        ImageTransformerInterface $transformer,
        int $quality
    ): void {
        \Phake::verify($transformer, \Phake::times(1))->setCompressionQuality($quality);
    }

    private function givenImageHasParametersWithQuality(Image $image, int $quality): void
    {
        $parameters = \Phake::mock(ImageParameters::class);
        \Phake::when($image)->getParameters()->thenReturn($parameters);
        \Phake::when($parameters)->getQuality()->thenReturn($quality);
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

    private function givenImage_getParameters_returnsImageParameters(Image $image): ImageParameters
    {
        $parameters = \Phake::mock(ImageParameters::class);
        \Phake::when($image)->getParameters()->thenReturn($parameters);

        return $parameters;
    }

    private function assertImage_getData_isCalledOnce(Image $image): void
    {
        \Phake::verify($image, \Phake::times(1))->getData();
    }

    private function givenImageFactory_createImageFromStream_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createImageFromStream(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }

    private function assertImageFactory_createImageFromStream_isCalledOnceWithStreamAndImageParameters(
        StreamInterface $stream,
        ImageParameters $imageParameters
    ): void {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createImageFromStream($stream, $imageParameters);
    }
}
