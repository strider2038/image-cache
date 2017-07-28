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
use Strider2038\ImgCache\Imaging\Extraction\Request\ThumbnailRequestConfigurationInterface;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageFactory;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
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
        $extractedImage = $this->givenExtractedImage();
        $this->givenProcessingImage($extractedImage);

        $thumbnailImage = $factory->create($requestConfiguration, $extractedImage);

        $this->assertInstanceOf(ProcessingImageInterface::class, $thumbnailImage);
    }

    public function testCreate_RequestConfigurationHasTransformations_TransformationsAppliedToProcessingImage(): void
    {
        $factory = $this->createThumbnailImageFactory();
        $transformation = $this->givenTransformation();
        $transformations = $this->givenTransformationsCollectionWithTransformation($transformation);
        $requestConfiguration = $this->givenThumbnailRequestConfiguration($transformations);
        $extractedImage = $this->givenExtractedImage();
        $processingImage = $this->givenProcessingImage($extractedImage);

        $thumbnailImage = $factory->create($requestConfiguration, $extractedImage);

        $this->assertInstanceOf(ProcessingImageInterface::class, $thumbnailImage);
        $this->assertTransformationIsAppliedToProcessingImage($transformation, $processingImage);
    }

    public function testCreate_RequestConfigurationIsGiven_GetSaveOptionsIsCalled(): void
    {
        $factory = $this->createThumbnailImageFactory();
        $saveOptions = $this->givenSaveOptions();
        $requestConfiguration = $this->givenThumbnailRequestConfiguration(null, $saveOptions);
        $extractedImage = $this->givenExtractedImage();
        $processingImage = $this->givenProcessingImage($extractedImage);

        $thumbnailImage = $factory->create($requestConfiguration, $extractedImage);

        $this->assertInstanceOf(SaveOptions::class, $thumbnailImage->getSaveOptions());
        $this->assertGetSaveOptionsIsCalledOnRequestConfiguration($requestConfiguration);
        $this->assertSaveOptionsIsCalledOnProcessingImage($processingImage, $saveOptions);
    }

    private function createThumbnailImageFactory(): ThumbnailImageFactory
    {
        $factory = new ThumbnailImageFactory($this->processingEngine);

        return $factory;
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

    private function givenThumbnailRequestConfiguration(
        TransformationsCollection $transformations = null,
        SaveOptions $saveOptions = null
    ): ThumbnailRequestConfigurationInterface {
        $requestConfiguration = \Phake::mock(ThumbnailRequestConfigurationInterface::class);

        if ($transformations === null) {
            $transformations = $this->givenEmptyTransformationsCollection();
        }

        \Phake::when($requestConfiguration)
            ->getTransformations()
            ->thenReturn($transformations);

        if ($saveOptions === null) {
            $saveOptions = \Phake::mock(SaveOptions::class);
        }

        \Phake::when($requestConfiguration)
            ->getSaveOptions()
            ->thenReturn($saveOptions);

        return $requestConfiguration;
    }

    private function givenProcessingImage(ImageInterface $extractedImage): ProcessingImageInterface
    {
        $processingImage = \Phake::mock(ProcessingImageInterface::class);

        \Phake::when($extractedImage)
            ->open($this->processingEngine)
            ->thenReturn($processingImage);

        return $processingImage;
    }

    private function givenExtractedImage(): ImageInterface
    {
        $extractedImage = \Phake::mock(ImageInterface::class);

        return $extractedImage;
    }

    private function assertTransformationIsAppliedToProcessingImage(
        TransformationInterface $transformation,
        ProcessingImageInterface $processingImage
    ): void {
        \Phake::verify($transformation, \Phake::times(1))
            ->apply($processingImage);
    }

    private function assertGetSaveOptionsIsCalledOnRequestConfiguration(
        ThumbnailRequestConfigurationInterface $requestConfiguration
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
