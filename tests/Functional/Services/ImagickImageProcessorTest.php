<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional\Services;

use Strider2038\ImgCache\Core\Streaming\ResourceStream;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactory;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessor;
use Strider2038\ImgCache\Imaging\Processing\Point;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ShiftingTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationCollection;
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
        $container = $this->loadContainer('services/imagick-image-processor.yml');
        $this->imageProcessor = $container->get('image_processor');
        $this->imageFactory = $container->get('image_factory');
    }

    /** @test */
    public function saveImageToFile_givenImageAndFilenameInSubdirectory_imageIsSaved(): void
    {
        $this->givenImageJpeg(self::JPEG_ORIGINAL_FILENAME);
        $image = $this->givenImage(self::JPEG_ORIGINAL_FILENAME);

        $this->imageProcessor->saveImageToFile($image, self::JPEG_FILENAME_IN_SUBDIRECTORY);

        $this->assertFileExists(self::JPEG_FILENAME_IN_SUBDIRECTORY);
        $this->assertFileHasMimeType(self::JPEG_FILENAME_IN_SUBDIRECTORY, self::MIME_TYPE_JPEG);
    }

    /** @test */
    public function saveImageToFile_givenPngImageAndJpgFilename_imageIsConvertedToJpgAndSaved(): void
    {
        $this->givenImagePng(self::PNG_ORIGINAL_FILENAME);
        $image = $this->givenImage(self::PNG_ORIGINAL_FILENAME);

        $this->imageProcessor->saveImageToFile($image, self::JPEG_FILENAME);

        $this->assertFileExists(self::JPEG_FILENAME);
        $this->assertFileHasMimeType(self::JPEG_FILENAME, self::MIME_TYPE_JPEG);
    }

    /** @test */
    public function saveImageToFile_givenJpegImageAndPngFilename_imageIsConvertedToPngAndSaved(): void
    {
        $this->givenImageJpeg(self::JPEG_ORIGINAL_FILENAME);
        $image = $this->givenImage(self::JPEG_ORIGINAL_FILENAME);

        $this->imageProcessor->saveImageToFile($image, self::PNG_FILENAME);

        $this->assertFileExists(self::PNG_FILENAME);
        $this->assertFileHasMimeType(self::PNG_FILENAME, self::MIME_TYPE_PNG);
    }

    /** @test */
    public function transformImage_givenJpegImageAndShiftingTransformation_imageIsShiftedAndReturned(): void
    {
        $this->givenImageJpeg(self::JPEG_ORIGINAL_FILENAME);
        $image = $this->givenImage(self::JPEG_ORIGINAL_FILENAME);
        $transformations = new TransformationCollection([
            new ShiftingTransformation(
                new Point(10, 0)
            )
        ]);

        $transformedImage = $this->imageProcessor->transformImage($image, $transformations);

        $this->assertInstanceOf(Image::class, $transformedImage);
    }

    private function givenImage(string $filename): Image
    {
        $file = fopen($filename, ResourceStreamModeEnum::READ_ONLY);
        $stream = new ResourceStream($file);

        return $this->imageFactory->createImageFromStream($stream);
    }
}
