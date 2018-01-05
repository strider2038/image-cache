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
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageExtractor;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ProcessingConfigurationParserInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCollection;

class ThumbnailImageExtractorTest extends TestCase
{
    private const FILENAME = '/publicFilename_configString.jpg';
    private const THUMBNAIL_FILENAME_VALUE = '/publicFilename.jpg';
    private const PROCESSING_CONFIGURATION = 'processing_configuration';

    /** @var ThumbnailFilenameParserInterface */
    private $filenameParser;

    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    protected function setUp(): void
    {
        $this->filenameParser = \Phake::mock(ThumbnailFilenameParserInterface::class);
        $this->processingConfigurationParser = \Phake::mock(ProcessingConfigurationParserInterface::class);
        $this->storageAccessor = \Phake::mock(StorageAccessorInterface::class);
        $this->imageProcessor = \Phake::mock(ImageProcessorInterface::class);
    }

    /** @test */
    public function getProcessedImage_imageExistsInSource_imageIsProcessedAndReturned(): void
    {
        $extractor = $this->createThumbnailImageExtractor();
        $thumbnailFilename = $this->givenFilenameParser_getParsedFilename_returnsThumbnailFilename();
        $sourceImage = $this->givenStorageAccessor_getImage_returnsImage();
        $processingConfiguration = $this->givenProcessingConfigurationParser_parseConfiguration_returnsProcessingConfiguration();
        $transformations = $this->givenProcessingConfiguration_getTransformations_returnsTransformationCollection($processingConfiguration);
        $imageParameters = $this->givenProcessingConfiguration_getImageParameters_returnsImageParameters($processingConfiguration);
        $processedImage = $this->givenImageProcessor_transformImage_returnsImage();

        $extractedImage = $extractor->getProcessedImage(self::FILENAME);

        $this->assertThumbnailFilename_getValue_isCalledOnce($thumbnailFilename);
        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertStorageAccessor_getImage_isCalledOnceWithFilename(self::THUMBNAIL_FILENAME_VALUE);
        $this->assertThumbnailFilename_getProcessingConfiguration_isCalledOnce($thumbnailFilename);
        $this->assertProcessingConfigurationParser_parseConfiguration_isCalledOnceWithConfiguration(self::PROCESSING_CONFIGURATION);
        $this->assertProcessingConfiguration_getTransformations_isCalledOnce($processingConfiguration);
        $this->assertProcessingConfiguration_getImageParameters_isCalledOnce($processingConfiguration);
        $this->assertImageProcessor_transformImage_isCalledOnceWithImageAndTransformationCollection($sourceImage, $transformations);
        $this->assertImage_setParameters_isCalledOnceWithParameters($processedImage, $imageParameters);
        $this->assertSame($processedImage, $extractedImage);
    }

    private function createThumbnailImageExtractor(): ThumbnailImageExtractor
    {
        return new ThumbnailImageExtractor(
            $this->filenameParser,
            $this->processingConfigurationParser,
            $this->storageAccessor,
            $this->imageProcessor
        );
    }

    private function givenStorageAccessor_getImage_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->storageAccessor)->getImage(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }

    private function givenFilenameParser_getParsedFilename_returnsThumbnailFilename(): ThumbnailFilename
    {
        $thumbnailFilename = \Phake::mock(ThumbnailFilename::class);
        \Phake::when($thumbnailFilename)->getValue()->thenReturn(self::THUMBNAIL_FILENAME_VALUE);
        \Phake::when($thumbnailFilename)->getProcessingConfiguration()->thenReturn(self::PROCESSING_CONFIGURATION);
        \Phake::when($this->filenameParser)->getParsedFilename(\Phake::anyParameters())->thenReturn($thumbnailFilename);

        return $thumbnailFilename;
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

    private function assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->filenameParser, \Phake::times(1))->getParsedFilename($filename);
    }

    private function assertStorageAccessor_getImage_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->storageAccessor, \Phake::times(1))->getImage($filename);
    }

    private function assertImageProcessor_transformImage_isCalledOnceWithImageAndTransformationCollection(
        Image $image,
        TransformationCollection $transformationCollection
    ): void {
        \Phake::verify($this->imageProcessor, \Phake::times(1))->transformImage($image, $transformationCollection);
    }

    private function assertThumbnailFilename_getValue_isCalledOnce(ThumbnailFilename $thumbnailFilename): void
    {
        \Phake::verify($thumbnailFilename, \Phake::times(1))->getValue();
    }

    private function assertThumbnailFilename_getProcessingConfiguration_isCalledOnce(ThumbnailFilename $thumbnailFilename): void
    {
        \Phake::verify($thumbnailFilename, \Phake::times(1))->getProcessingConfiguration();
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
