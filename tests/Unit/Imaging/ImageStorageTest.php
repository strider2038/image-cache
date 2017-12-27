<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Extraction\ImageExtractorInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\ImageStorage;
use Strider2038\ImgCache\Imaging\Insertion\ImageWriterInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ImageStorageTest extends TestCase
{
    use ProviderTrait;

    private const IMAGE_FILENAME_VALUE = 'image_filename.jpg';
    private const FILE_NAME_MASK = 'file_name_mask';

    /** @var ImageExtractorInterface */
    private $imageExtractor;

    /** @var ImageWriterInterface */
    private $imageWriter;

    protected function setUp(): void
    {
        $this->imageExtractor = \Phake::mock(ImageExtractorInterface::class);
        $this->imageWriter = \Phake::mock(ImageWriterInterface::class);
    }

    /** @test */
    public function getImage_givenKeyAndExtractorReturnsImage_imageReturned(): void
    {
        $storage = $this->createImageStorage();
        $extractedImage = $this->givenImageExtractor_getProcessedImage_returnsImage();
        $filename = $this->givenImageFilename(self::IMAGE_FILENAME_VALUE);

        $image = $storage->getImage($filename);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertImageExtractor_getProcessedImage_isCalledOnceWithFilename(self::IMAGE_FILENAME_VALUE);
        $this->assertSame($extractedImage, $image);
    }

    /** @test */
    public function putImage_givenKeyAndImage_imageInsertedToSource(): void
    {
        $storage = $this->createImageStorage();
        $image = $this->givenImage();
        $filename = $this->givenImageFilename(self::IMAGE_FILENAME_VALUE);

        $storage->putImage($filename, $image);

        $this->assertImageWriter_insertImage_isCalledOnceWith(self::IMAGE_FILENAME_VALUE, $image);
    }

    /**
     * @test
     * @dataProvider boolValuesProvider
     * @param bool $expectedExists
     */
    public function imageExists_givenKey_existsStatusReturned(bool $expectedExists): void
    {
        $storage = $this->createImageStorage();
        $this->givenImageWriter_imageExists_returns($expectedExists);
        $filename = $this->givenImageFilename(self::IMAGE_FILENAME_VALUE);

        $exists = $storage->imageExists($filename);

        $this->assertEquals($expectedExists, $exists);
        $this->assertImageWriter_imageExists_isCalledOnceWith(self::IMAGE_FILENAME_VALUE);
    }

    /** @test */
    public function deleteImage_givenKey_imageDeletedFromSource(): void
    {
        $storage = $this->createImageStorage();
        $filename = $this->givenImageFilename(self::IMAGE_FILENAME_VALUE);

        $storage->deleteImage($filename);

        $this->assertImageWriter_deleteImage_isCalledOnceWith(self::IMAGE_FILENAME_VALUE);
    }

    /** @test */
    public function getImageFileNameMask_givenKey_fileNameMaskReturned(): void
    {
        $storage = $this->createImageStorage();
        $this->givenImageWriter_getImageFileNameMask_returnsFileNameMask();
        $filename = $this->givenImageFilename(self::IMAGE_FILENAME_VALUE);

        $mask = $storage->getImageFileNameMask($filename);

        $this->assertImageWriter_getImageFileNameMask_isCalledOnceWith(self::IMAGE_FILENAME_VALUE);
        $this->assertEquals(self::FILE_NAME_MASK, $mask);
    }

    private function createImageStorage(): ImageStorage
    {
        return new ImageStorage($this->imageExtractor, $this->imageWriter);
    }

    private function givenImageFilename(string $value): ImageFilenameInterface
    {
        $filename = \Phake::mock(ImageFilenameInterface::class);
        \Phake::when($filename)->__toString()->thenReturn($value);

        return $filename;
    }

    private function assertImageExtractor_getProcessedImage_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->imageExtractor, \Phake::times(1))->getProcessedImage($filename);
    }

    private function givenImageExtractor_getProcessedImage_returnsImage(): Image
    {
        $extractedImage = \Phake::mock(Image::class);
        \Phake::when($this->imageExtractor)->getProcessedImage(\Phake::anyParameters())->thenReturn($extractedImage);

        return $extractedImage;
    }

    private function assertImageWriter_insertImage_isCalledOnceWith(string $key, Image $image): void
    {
        \Phake::verify($this->imageWriter, \Phake::times(1))->insertImage($key, $image);
    }

    private function assertImageWriter_deleteImage_isCalledOnceWith(string $key): void
    {
        \Phake::verify($this->imageWriter, \Phake::times(1))->deleteImage($key);
    }

    private function assertImageWriter_getImageFileNameMask_isCalledOnceWith(string $key): void
    {
        \Phake::verify($this->imageWriter, \Phake::times(1))->getImageFileNameMask($key);
    }

    private function assertImageWriter_imageExists_isCalledOnceWith(string $key): void
    {
        \Phake::verify($this->imageWriter, \Phake::times(1))->imageExists($key);
    }

    private function givenImageWriter_imageExists_returns(bool $expectedExists): void
    {
        \Phake::when($this->imageWriter)->imageExists(\Phake::anyParameters())->thenReturn($expectedExists);
    }

    private function givenImageWriter_getImageFileNameMask_returnsFileNameMask(): void
    {
        \Phake::when($this->imageWriter)->getImageFileNameMask(\Phake::anyParameters())->thenReturn(self::FILE_NAME_MASK);
    }

    private function givenImage(): Image
    {
        return \Phake::mock(Image::class);
    }
}
