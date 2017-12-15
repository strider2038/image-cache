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
use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\ImageCache;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;

class ImageCacheTest extends TestCase
{
    use FileOperationsTrait;

    private const WEB_DIRECTORY = 'web_directory/';
    private const FILE_NAME = 'file_name';
    private const CACHE_FILE_NAME = self::WEB_DIRECTORY . self::FILE_NAME;
    private const FOUND_FILE_NAME = self::WEB_DIRECTORY . '/found';

    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    protected function setUp(): void
    {
        $this->fileOperations = \Phake::mock(FileOperationsInterface::class);
        $this->imageProcessor = \Phake::mock(ImageProcessorInterface::class);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Directory .* does not exist/
     */
    public function construct_cacheDirectoryIsInvalid_exceptionThrown(): void
    {
        $this->givenFileOperations_isDirectory_returns($this->fileOperations, self::WEB_DIRECTORY, false);

        new ImageCache(self::WEB_DIRECTORY, $this->fileOperations, $this->imageProcessor);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* does not exist/
     */
    public function getImage_fileDoesNotExist_exceptionThrown(): void
    {
        $cache = $this->createImageCache();
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::CACHE_FILE_NAME, false);
        $filename = $this->givenImageFilename(self::FILE_NAME);

        $cache->getImage($filename);
    }

    /** @test */
    public function getImage_fileExists_imageFileReturned(): void
    {
        $cache = $this->createImageCache();
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::CACHE_FILE_NAME, true);
        $filename = $this->givenImageFilename(self::FILE_NAME);

        $image = $cache->getImage($filename);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertEquals(self::CACHE_FILE_NAME, $image->getFilename());
    }

    /** @test */
    public function putImage_givenFileNameAndImage_imageIsSavedInCache(): void
    {
        $cache = $this->createImageCache();
        $image = $this->givenImage();
        $filename = $this->givenImageFilename(self::FILE_NAME);

        $cache->putImage($filename, $image);

        $this->assertImageProcessor_saveToFile_isCalledOnceWith($image, self::CACHE_FILE_NAME);
    }

    /** @test */
    public function deleteImagesByMask_givenFileNameMask_allImagesDeletedFromCache(): void
    {
        $cache = $this->createImageCache();
        $this->givenFileOperations_findByMask_returnsStringListWithValues($this->fileOperations, [self::FOUND_FILE_NAME]);

        $cache->deleteImagesByMask(self::FILE_NAME);

        $this->assertFileOperations_findByMask_isCalledOnceWith($this->fileOperations, self::CACHE_FILE_NAME);
        $this->assertFileOperations_deleteFile_isCalledOnce($this->fileOperations, self::FOUND_FILE_NAME);
    }

    private function createImageCache(): ImageCache
    {
        $this->givenFileOperations_isDirectory_returns($this->fileOperations, self::WEB_DIRECTORY, true);

        return new ImageCache(self::WEB_DIRECTORY, $this->fileOperations, $this->imageProcessor);
    }

    private function givenImage(): Image
    {
        return \Phake::mock(Image::class);
    }

    private function givenImageFilename(string $value): ImageFilenameInterface
    {
        $filename = \Phake::mock(ImageFilenameInterface::class);
        \Phake::when($filename)->__toString()->thenReturn($value);

        return $filename;
    }

    private function assertImageProcessor_saveToFile_isCalledOnceWith(Image $image, string $fileName): void
    {
        \Phake::verify($this->imageProcessor, \Phake::times(1))->saveToFile($image, $fileName);
    }
}
