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
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Processing\DeprecatedImageProcessor;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;

class DeprecatedImageProcessorTest extends TestCase
{
    use ImageTrait;

    /** @var ProcessingEngineInterface */
    private $processingEngine;

    protected function setUp()
    {
        $this->processingEngine = \Phake::mock(ProcessingEngineInterface::class);
    }

    public function testProcess_NoTransformationsInRequestConfiguration_TransformationsAreNotAppliedToProcessingImage(): void
    {
        $processor = $this->createImageProcessor();
        $configuration = $this->givenProcessingConfiguration();
        $sourceImage = $this->givenImage();
        $processingImage = $this->givenProcessingEngine_Open_ReturnsProcessingImage($sourceImage);

        $processedImage = $processor->process($configuration, $sourceImage);

        $this->assertInstanceOf(ProcessingImageInterface::class, $processedImage);
        $this->assertSame($processingImage, $processedImage);
    }

    public function testProcess_ConfigurationHasTransformations_TransformationsAppliedToProcessingImage(): void
    {
        $processor = $this->createImageProcessor();
        $transformation = $this->givenTransformation();
        $transformations = $this->givenTransformationsCollectionWithTransformation($transformation);
        $requestConfiguration = $this->givenProcessingConfiguration($transformations);
        $sourceImage = $this->givenImage();
        $processingImage = $this->givenProcessingEngine_Open_ReturnsProcessingImage($sourceImage);

        $processedImage = $processor->process($requestConfiguration, $sourceImage);

        $this->assertInstanceOf(ProcessingImageInterface::class, $processedImage);
        $this->assertSame($processingImage, $processedImage);
        $this->assertTransformationIsAppliedToProcessingImage($transformation, $processingImage);
    }

    public function testProcess_ConfigurationIsGiven_GetSaveOptionsIsCalled(): void
    {
        $processor = $this->createImageProcessor();
        $saveOptions = $this->givenSaveOptions();
        $configuration = $this->givenProcessingConfiguration(null, $saveOptions);
        $sourceImage = $this->givenImage();
        $processingImage = $this->givenProcessingEngine_Open_ReturnsProcessingImage($sourceImage);

        $processedImage = $processor->process($configuration, $sourceImage);

        $this->assertInstanceOf(SaveOptions::class, $processedImage->getSaveOptions());
        $this->assertGetSaveOptionsIsCalledOnRequestConfiguration($configuration);
        $this->assertSaveOptionsIsCalledOnProcessingImage($processingImage, $saveOptions);
    }

    private function createImageProcessor(): DeprecatedImageProcessor
    {
        $processor = new DeprecatedImageProcessor($this->processingEngine);

        return $processor;
    }

    private function givenEmptyTransformationsCollection(): TransformationsCollection
    {
        $transformations = \Phake::mock(TransformationsCollection::class);

        $traversable = \Phake::mock(\Traversable::class);

        \Phake::when($transformations)
            ->getIterator()
            ->thenReturn($traversable);

        return $transformations;
    }

    private function givenSaveOptions(): SaveOptions
    {
        $saveOptions = \Phake::mock(SaveOptions::class);

        return $saveOptions;
    }

    private function givenTransformation(): TransformationInterface
    {
        $transformation = \Phake::mock(TransformationInterface::class);

        return $transformation;
    }

    private function givenTransformationsCollectionWithTransformation(
        TransformationInterface $transformation
    ): TransformationsCollection {
        $transformations = new TransformationsCollection();

        $transformations->add($transformation);

        return $transformations;
    }

    private function givenProcessingConfiguration(
        TransformationsCollection $transformations = null,
        SaveOptions $saveOptions = null
    ): ProcessingConfigurationInterface {
        $configuration = \Phake::mock(ProcessingConfigurationInterface::class);

        if ($transformations === null) {
            $transformations = $this->givenEmptyTransformationsCollection();
        }

        \Phake::when($configuration)
            ->getTransformations()
            ->thenReturn($transformations);

        if ($saveOptions === null) {
            $saveOptions = \Phake::mock(SaveOptions::class);
        }

        \Phake::when($configuration)
            ->getSaveOptions()
            ->thenReturn($saveOptions);

        return $configuration;
    }

    private function givenProcessingEngine_Open_ReturnsProcessingImage(ImageInterface $extractedImage): ProcessingImageInterface
    {
        $processingImage = \Phake::mock(ProcessingImageInterface::class);

        \Phake::when($extractedImage)
            ->open($this->processingEngine)
            ->thenReturn($processingImage);

        return $processingImage;
    }

    private function assertTransformationIsAppliedToProcessingImage(
        TransformationInterface $transformation,
        ProcessingImageInterface $processingImage
    ): void {
        \Phake::verify($transformation, \Phake::times(1))
            ->apply($processingImage);
    }

    private function assertGetSaveOptionsIsCalledOnRequestConfiguration(
        ProcessingConfigurationInterface $requestConfiguration
    ): void {
        \Phake::verify($requestConfiguration, \Phake::times(1))->getSaveOptions();
    }

    private function assertSaveOptionsIsCalledOnProcessingImage(
        ProcessingImageInterface $processingImage,
        SaveOptions $saveOptions
    ): void {
        \Phake::verify($processingImage, \Phake::times(1))->setSaveOptions($saveOptions);
    }
}
