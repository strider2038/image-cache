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

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Extraction\Request\FileExtractionRequestInterface;
use Strider2038\ImgCache\Imaging\Extraction\Request\ThumbnailRequestConfiguration;
use Strider2038\ImgCache\Imaging\Extraction\Result\ExtractedImageInterface;
use Strider2038\ImgCache\Imaging\Extraction\Result\ThumbnailImage;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageFactory;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;

class ThumbnailImageFactoryTest extends TestCase
{
    /** @var ProcessingEngineInterface */
    private $processingEngine;

    protected function setUp()
    {
        $this->processingEngine = \Phake::mock(ProcessingEngineInterface::class);
    }

    public function testCreate_NoTransformationsInRequestConfiguration_TransformationsAreNotAppliedToProcessingImage(): void
    {
        $factory = $this->createThumbnailImageFactory();
        $requestConfiguration = $this->givenThumbnailRequestConfiguration();
        $extractedImage = \Phake::mock(ExtractedImageInterface::class);
        $processingImage = $this->givenProcessingImage($extractedImage);

        $thumbnailImage = $factory->create($requestConfiguration, $extractedImage);

        $this->assertInstanceOf(ThumbnailImage::class, $thumbnailImage);
        \Phake::verifyNoInteraction($processingImage);
    }

    public function testCreate_RequestConfigurationHasTransformations_TransformationsAppliedToProcessingImage(): void
    {
        $factory = $this->createThumbnailImageFactory();
        $requestConfiguration = $this->givenThumbnailRequestConfiguration();
        $transformation = $this->thumbnailRequestConfigurationHasTransformation($requestConfiguration);
        $extractedImage = \Phake::mock(ExtractedImageInterface::class);
        $processingImage = $this->givenProcessingImage($extractedImage);

        $thumbnailImage = $factory->create($requestConfiguration, $extractedImage);

        $this->assertInstanceOf(ThumbnailImage::class, $thumbnailImage);
        \Phake::verify($transformation, \Phake::times(1))->apply($processingImage);
    }

    public function testCreate_NoSaveOptionsInRequestConfiguration_ThumbnailImageHasNoSaveOptions(): void
    {
        $factory = $this->createThumbnailImageFactory();
        $requestConfiguration = $this->givenThumbnailRequestConfiguration();
        $extractedImage = \Phake::mock(ExtractedImageInterface::class);
        $processingImage = $this->givenProcessingImage($extractedImage);

        $thumbnailImage = $factory->create($requestConfiguration, $extractedImage);

        $this->assertNull($thumbnailImage->getSaveOptions());
        \Phake::verifyNoInteraction($processingImage);
    }

    public function testCreate_RequestConfigurationHasSaveOptions_ThumbnailImageHasSaveOptions(): void
    {
        $factory = $this->createThumbnailImageFactory();
        $requestConfiguration = $this->givenThumbnailRequestConfiguration();
        $this->thumbnailRequestConfigurationHasSaveOptions($requestConfiguration);
        $extractedImage = \Phake::mock(ExtractedImageInterface::class);
        $processingImage = $this->givenProcessingImage($extractedImage);

        $thumbnailImage = $factory->create($requestConfiguration, $extractedImage);

        $this->assertInstanceOf(SaveOptions::class, $thumbnailImage->getSaveOptions());
        \Phake::verifyNoInteraction($processingImage);
    }

    private function createThumbnailImageFactory(): ThumbnailImageFactory
    {
        $factory = new ThumbnailImageFactory($this->processingEngine);

        return $factory;
    }

    private function givenThumbnailRequestConfiguration(): ThumbnailRequestConfiguration
    {
        $extractionRequest = \Phake::mock(FileExtractionRequestInterface::class);
        $requestConfiguration = new ThumbnailRequestConfiguration($extractionRequest);

        return $requestConfiguration;
    }

    private function thumbnailRequestConfigurationHasTransformation(
        ThumbnailRequestConfiguration $requestConfiguration
    ): TransformationInterface {
        $transformation = \Phake::mock(TransformationInterface::class);
        $transformations = new TransformationsCollection();
        $transformations->add($transformation);

        $requestConfiguration->setTransformations($transformations);

        return $transformation;
    }

    private function thumbnailRequestConfigurationHasSaveOptions(
        ThumbnailRequestConfiguration $requestConfiguration
    ): SaveOptions {
        $saveOptions = new SaveOptions();

        $requestConfiguration->setSaveOptions($saveOptions);

        return $saveOptions;
    }

    private function givenProcessingImage(ExtractedImageInterface $extractedImage): ProcessingImageInterface
    {
        $processingImage = \Phake::mock(ProcessingImageInterface::class);
        \Phake::when($extractedImage)
            ->open($this->processingEngine)
            ->thenReturn($processingImage);

        return $processingImage;
    }
}
