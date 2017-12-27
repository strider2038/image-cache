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
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

class ThumbnailImageExtractorTest extends TestCase
{
    private const FILENAME = '/publicFilename_configString.jpg';
    private const THUMBNAIL_FILENAME_VALUE = '/publicFilename.jpg';

    /** @var ThumbnailFilenameParserInterface */
    private $filenameParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    protected function setUp(): void
    {
        $this->filenameParser = \Phake::mock(ThumbnailFilenameParserInterface::class);
        $this->storageAccessor = \Phake::mock(StorageAccessorInterface::class);
        $this->imageProcessor = \Phake::mock(ImageProcessorInterface::class);
    }

    /** @test */
    public function getProcessedImage_imageExistsInSource_imageIsProcessedAndReturned(): void
    {
        $extractor = $this->createThumbnailImageExtractor();
        $thumbnailKey = $this->givenFilenameParser_getParsedFilename_returnsThumbnailFilename();
        $processingConfiguration = $this->givenThumbnailFilename_getProcessingConfiguration_returnsProcessingConfiguration($thumbnailKey);
        $image = $this->givenStorageAccessor_getImage_returnsImage();
        $processedImage = $this->givenImageProcessor_process_returnsProcessedImage();

        $extractedImage = $extractor->getProcessedImage(self::FILENAME);

        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertStorageAccessor_getImage_isCalledOnceWithFilename(self::THUMBNAIL_FILENAME_VALUE);
        $this->assertImageProcessor_process_isCalledOnceWithImageAndProcessingConfiguration($image, $processingConfiguration);
        $this->assertSame($processedImage, $extractedImage);
    }

    private function createThumbnailImageExtractor(): ThumbnailImageExtractor
    {
        $extractor = new ThumbnailImageExtractor(
            $this->filenameParser,
            $this->storageAccessor,
            $this->imageProcessor
        );
        return $extractor;
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
        \Phake::when($this->filenameParser)->getParsedFilename(\Phake::anyParameters())->thenReturn($thumbnailFilename);

        return $thumbnailFilename;
    }

    private function givenImageProcessor_process_returnsProcessedImage(): Image
    {
        $processedImage = \Phake::mock(Image::class);

        \Phake::when($this->imageProcessor)
            ->process(\Phake::anyParameters())
            ->thenReturn($processedImage);

        return $processedImage;
    }

    private function givenThumbnailFilename_getProcessingConfiguration_returnsProcessingConfiguration(
        ThumbnailFilename $thumbnailFilename
    ): ProcessingConfiguration {
        $processingConfiguration = \Phake::mock(ProcessingConfiguration::class);
        \Phake::when($thumbnailFilename)->getProcessingConfiguration()->thenReturn($processingConfiguration);

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

    private function assertImageProcessor_process_isCalledOnceWithImageAndProcessingConfiguration(
        Image $image,
        ProcessingConfiguration $processingConfiguration
    ): void {
        \Phake::verify($this->imageProcessor, \Phake::times(1))->process($image, $processingConfiguration);
    }
}
