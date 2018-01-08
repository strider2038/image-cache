<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Extraction;

use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageCreator;
use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ProcessingConfigurationParserInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCollection;

class ThumbnailImageCreatorTest extends TestCase
{
    private const PROCESSING_CONFIGURATION = 'processing_configuration';

    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    protected function setUp(): void
    {
        $this->processingConfigurationParser = \Phake::mock(ProcessingConfigurationParserInterface::class);
        $this->imageProcessor = \Phake::mock(ImageProcessorInterface::class);
    }

    /** @test */
    public function createThumbnailImageByConfiguration_givenImageAndProcessingConfiguration_processedImageReturned(): void
    {
        $thumbnailImageCreator = $this->createThumbnailImageCreator();
        $image = \Phake::mock(Image::class);
        $processingConfiguration = $this->givenProcessingConfigurationParser_parseConfiguration_returnsProcessingConfiguration();
        $transformations = $this->givenProcessingConfiguration_getTransformations_returnsTransformationCollection($processingConfiguration);
        $imageParameters = $this->givenProcessingConfiguration_getImageParameters_returnsImageParameters($processingConfiguration);
        $processedImage = $this->givenImageProcessor_transformImage_returnsImage();

        $thumbnailImage = $thumbnailImageCreator->createThumbnailImageByConfiguration(
            $image,
            self::PROCESSING_CONFIGURATION
        );

        $this->assertProcessingConfigurationParser_parseConfiguration_isCalledOnceWithConfiguration(self::PROCESSING_CONFIGURATION);
        $this->assertProcessingConfiguration_getTransformations_isCalledOnce($processingConfiguration);
        $this->assertProcessingConfiguration_getImageParameters_isCalledOnce($processingConfiguration);
        $this->assertImageProcessor_transformImage_isCalledOnceWithImageAndTransformationCollection($image, $transformations);
        $this->assertImage_setParameters_isCalledOnceWithParameters($processedImage, $imageParameters);
        $this->assertSame($processedImage, $thumbnailImage);
    }

    private function createThumbnailImageCreator(): ThumbnailImageCreator
    {
        return new ThumbnailImageCreator($this->processingConfigurationParser, $this->imageProcessor);
    }

    private function givenImageProcessor_transformImage_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);

        \Phake::when($this->imageProcessor)
            ->transformImage(\Phake::anyParameters())
            ->thenReturn($image);

        return $image;
    }

    private function givenProcessingConfigurationParser_parseConfiguration_returnsProcessingConfiguration(): ProcessingConfiguration
    {
        $processingConfiguration = \Phake::mock(ProcessingConfiguration::class);
        \Phake::when($this->processingConfigurationParser)
            ->parseConfiguration(\Phake::anyParameters())
            ->thenReturn($processingConfiguration);

        return $processingConfiguration;
    }

    private function assertImageProcessor_transformImage_isCalledOnceWithImageAndTransformationCollection(
        Image $image,
        TransformationCollection $transformationCollection
    ): void {
        \Phake::verify($this->imageProcessor, \Phake::times(1))->transformImage($image, $transformationCollection);
    }

    private function assertProcessingConfigurationParser_parseConfiguration_isCalledOnceWithConfiguration(
        string $configuration
    ): void {
        \Phake::verify($this->processingConfigurationParser, \Phake::times(1))->parseConfiguration($configuration);
    }

    private function assertProcessingConfiguration_getTransformations_isCalledOnce(
        ProcessingConfiguration $processingConfiguration
    ): void {
        \Phake::verify($processingConfiguration, \Phake::times(1))->getTransformations();
    }

    private function assertProcessingConfiguration_getImageParameters_isCalledOnce(
        ProcessingConfiguration $processingConfiguration
    ): void {
        \Phake::verify($processingConfiguration, \Phake::times(1))->getImageParameters();
    }

    private function givenProcessingConfiguration_getTransformations_returnsTransformationCollection(
        ProcessingConfiguration $processingConfiguration
    ): TransformationCollection {
        $transformations = \Phake::mock(TransformationCollection::class);
        \Phake::when($processingConfiguration)->getTransformations()->thenReturn($transformations);

        return $transformations;
    }

    private function givenProcessingConfiguration_getImageParameters_returnsImageParameters(
        ProcessingConfiguration $processingConfiguration
    ): ImageParameters {
        $parameters = \Phake::mock(ImageParameters::class);
        \Phake::when($processingConfiguration)->getImageParameters()->thenReturn($parameters);

        return $parameters;
    }

    private function assertImage_setParameters_isCalledOnceWithParameters(
        Image $image,
        ImageParameters $imageParameters
    ): void {
        \Phake::verify($image, \Phake::times(1))->setParameters($imageParameters);
    }
}
