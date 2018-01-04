<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional;

use Strider2038\ImgCache\Imaging\Image\ImageFactory;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessor;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickImageProcessorTest extends FunctionalTestCase
{
    private const RUNTIME_SUBDIRECTORY = self::TEMPORARY_DIRECTORY . '/subdirectory';
    private const PNG_ORIGINAL_FILENAME = self::TEMPORARY_DIRECTORY . '/original.png';
    private const JPEG_ORIGINAL_FILENAME = self::TEMPORARY_DIRECTORY . '/original.jpg';
    private const PNG_FILENAME = self::TEMPORARY_DIRECTORY . '/image.png';
    private const JPEG_FILENAME = self::TEMPORARY_DIRECTORY . '/image.jpg';
    private const JPEG_FILENAME_IN_SUBDIRECTORY = self::RUNTIME_SUBDIRECTORY . '/image.jpg';

    /** @var ImageProcessor */
    private $imageProcessor;

    /** @var ImageFactory */
    private $imageFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $container = $this->loadContainer('imagick-image-processor.yml');
        $this->imageProcessor = $container->get('image_processor');
        $this->imageFactory = $container->get('image_factory');
    }

    /** @test */
    public function saveToFile_givenImageAndFilenameInSubdirectory_imageIsSaved(): void
    {
        $this->givenImageJpeg(self::JPEG_ORIGINAL_FILENAME);
        $image = $this->imageFactory->createImageFromFile(self::JPEG_ORIGINAL_FILENAME);

        $this->imageProcessor->saveToFile($image, self::JPEG_FILENAME_IN_SUBDIRECTORY);

        $this->assertFileExists(self::JPEG_FILENAME_IN_SUBDIRECTORY);
        $this->assertFileHasMimeType(self::JPEG_FILENAME_IN_SUBDIRECTORY, self::MIME_TYPE_JPEG);
    }

    /** @test */
    public function saveToFile_givenPngImageAndJpgFilename_imageIsConvertedToJpgAndSaved(): void
    {
        $this->givenImagePng(self::PNG_ORIGINAL_FILENAME);
        $image = $this->imageFactory->createImageFromFile(self::PNG_ORIGINAL_FILENAME);

        $this->imageProcessor->saveToFile($image, self::JPEG_FILENAME);

        $this->assertFileExists(self::JPEG_FILENAME);
        $this->assertFileHasMimeType(self::JPEG_FILENAME, self::MIME_TYPE_JPEG);
    }

    /** @test */
    public function saveToFile_givenJpegImageAndPngFilename_imageIsConvertedToPngAndSaved(): void
    {
        $this->givenImageJpeg(self::JPEG_ORIGINAL_FILENAME);
        $image = $this->imageFactory->createImageFromFile(self::JPEG_ORIGINAL_FILENAME);

        $this->imageProcessor->saveToFile($image, self::PNG_FILENAME);

        $this->assertFileExists(self::PNG_FILENAME);
        $this->assertFileHasMimeType(self::PNG_FILENAME, self::MIME_TYPE_PNG);
    }
}
