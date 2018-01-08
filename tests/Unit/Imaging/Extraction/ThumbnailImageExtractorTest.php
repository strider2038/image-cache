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
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageCreatorInterface;
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

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    /** @var ThumbnailImageCreatorInterface */
    private $thumbnailImageCreator;

    protected function setUp(): void
    {
        $this->filenameParser = \Phake::mock(ThumbnailFilenameParserInterface::class);
        $this->storageAccessor = \Phake::mock(StorageAccessorInterface::class);
        $this->thumbnailImageCreator = \Phake::mock(ThumbnailImageCreatorInterface::class);
    }

    /** @test */
    public function getProcessedImage_imageExistsInSource_imageIsProcessedAndReturned(): void
    {
        $extractor = $this->createThumbnailImageExtractor();
        $thumbnailFilename = $this->givenFilenameParser_getParsedFilename_returnsThumbnailFilename();
        $sourceImage = $this->givenStorageAccessor_getImage_returnsImage();
        $thumbnailImage = $this->givenThumbnailImageCreator_createThumbnailImageByConfiguration_returnsImage();

        $extractedImage = $extractor->getProcessedImage(self::FILENAME);

        $this->assertThumbnailFilename_getValue_isCalledOnce($thumbnailFilename);
        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertStorageAccessor_getImage_isCalledOnceWithFilename(self::THUMBNAIL_FILENAME_VALUE);
        $this->assertThumbnailFilename_getProcessingConfiguration_isCalledOnce($thumbnailFilename);
        $this->assertThumbnailImageCreator_createThumbnailImageByConfiguration_isCalledOnceWithImageAndConfiguration(
            $sourceImage,
            self::PROCESSING_CONFIGURATION
        );
        $this->assertSame($thumbnailImage, $extractedImage);
    }

    private function createThumbnailImageExtractor(): ThumbnailImageExtractor
    {
        return new ThumbnailImageExtractor(
            $this->filenameParser,
            $this->storageAccessor,
            $this->thumbnailImageCreator
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

    private function givenThumbnailImageCreator_createThumbnailImageByConfiguration_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);

        \Phake::when($this->thumbnailImageCreator)
            ->createThumbnailImageByConfiguration(\Phake::anyParameters())
            ->thenReturn($image);

        return $image;
    }

    private function assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->filenameParser, \Phake::times(1))->getParsedFilename($filename);
    }

    private function assertStorageAccessor_getImage_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->storageAccessor, \Phake::times(1))->getImage($filename);
    }

    private function assertThumbnailImageCreator_createThumbnailImageByConfiguration_isCalledOnceWithImageAndConfiguration(
        Image $image,
        string $configuration
    ): void {
        \Phake::verify($this->thumbnailImageCreator, \Phake::times(1))
            ->createThumbnailImageByConfiguration($image, $configuration);
    }

    private function assertThumbnailFilename_getValue_isCalledOnce(ThumbnailFilename $thumbnailFilename): void
    {
        \Phake::verify($thumbnailFilename, \Phake::times(1))->getValue();
    }

    private function assertThumbnailFilename_getProcessingConfiguration_isCalledOnce(ThumbnailFilename $thumbnailFilename): void
    {
        \Phake::verify($thumbnailFilename, \Phake::times(1))->getProcessingConfiguration();
    }
}
