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
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Extraction\ImageExtractorInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\ImageCache;
use Strider2038\ImgCache\Imaging\Insertion\ImageWriterInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;

class ImageCacheTest extends TestCase
{
    use FileOperationsTrait, ImageTrait;

    private const BASE_DIRECTORY = '/cache';
    private const INVALID_KEY = 'a';
    private const GET_KEY = '/a.jpg';
    private const GET_DESTINATION_FILENAME = self::BASE_DIRECTORY . '/a.jpg';
    private const INSERT_KEY = '/b.jpg';
    private const DELETE_KEY = '/c.jpg';
    private const DELETE_KEY_FILENAME_MASK = '/c*.jpg';
    private const DELETE_KEY_DESTINATION_FILENAME_MASK = self::BASE_DIRECTORY . '/c*.jpg';
    private const DELETE_KEY_DESTINATION_FILENAME = self::BASE_DIRECTORY . '/c.jpg';

    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var ImageExtractorInterface */
    private $imageExtractor;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    protected function setUp()
    {
        parent::setUp();
        $this->fileOperations = $this->givenFileOperations();
        $this->imageExtractor = \Phake::mock(ImageExtractorInterface::class);
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
        $this->givenFileOperations_isDirectory_returns($this->fileOperations, self::BASE_DIRECTORY, false);

        new ImageCache(
            self::BASE_DIRECTORY,
            $this->fileOperations,
            $this->imageProcessor,
            $this->imageExtractor
        );
    }

    /** @test */
    public function get_imageDoesNotExistInSource_nullIsReturned(): void
    {
        $cache = $this->createImageCache();
        $this->givenImageExtractor_extract_returnsNull();

        $image = $cache->get(self::GET_KEY);

        $this->assertNull($image);
    }

    /** @test */
    public function get_imageExistsInSource_sourceImageSavedToWebDirectoryAndCachedImageIsReturned(): void
    {
        $cache = $this->createImageCache();
        $image = $this->givenImageExtractor_extract_returnsImage(self::GET_KEY);

        $cachedImage = $cache->get(self::GET_KEY);

        $this->assertSame($cachedImage, $image);
        $this->assertImageProcessor_saveToFile_isCalledOnceWith($image);
    }

    /** @test */
    public function put_imageWriterIsSpecified_insertMethodCalled(): void
    {
        $writer = \Phake::mock(ImageWriterInterface::class);
        $cache = $this->createImageCache($writer);
        $stream = $this->givenStream();

        $cache->put(self::INSERT_KEY, $stream);

        \Phake::verify($writer, \Phake::times(1))->insert(self::INSERT_KEY, $stream);
    }

    /** @test */
    public function delete_imageWriterIsSpecified_deleteMethodCalled(): void
    {
        $writer = \Phake::mock(ImageWriterInterface::class);
        $cache = $this->createImageCache($writer);
        \Phake::when($writer)->getFileMask(self::DELETE_KEY)->thenReturn(self::DELETE_KEY_FILENAME_MASK);
        \Phake::when($this->fileOperations)
            ->findByMask(self::DELETE_KEY_DESTINATION_FILENAME_MASK)
            ->thenReturn(new StringList([self::DELETE_KEY_DESTINATION_FILENAME]));

        $cache->delete(self::DELETE_KEY);

        \Phake::verify($writer, \Phake::times(1))->delete(self::DELETE_KEY);
        $this->assertFileOperations_deleteFile_isCalledOnce($this->fileOperations, self::DELETE_KEY_DESTINATION_FILENAME);
    }

    /** @test */
    public function exists_keyIsSet_existsCalledWithKeyAndValueReturned(): void
    {
        $writer = \Phake::mock(ImageWriterInterface::class);
        $cache = $this->createImageCache($writer);
        \Phake::when($writer)->exists(self::GET_KEY)->thenReturn(true);

        $result = $cache->exists(self::GET_KEY);

        \Phake::verify($writer, \Phake::times(1))->exists(self::GET_KEY);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @param string $method
     * @param array $params
     * @dataProvider invalidKeyProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidValueException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Key must start with slash
     */
    public function givenMethod_givenInvalidKey_exceptionThrown(string $method, array $params): void
    {
        $cache = $this->createImageCache();

        call_user_func_array([$cache, $method], $params);
    }

    public function invalidKeyProvider(): array
    {
        return [
            ['get', ['']],
            ['get', [self::INVALID_KEY]],
            ['put', [self::INVALID_KEY, $this->givenStream()]],
            ['delete', [self::INVALID_KEY]],
            ['exists', [self::INVALID_KEY]],
        ];
    }

    private function createImageCache(ImageWriterInterface $writer = null): ImageCache
    {
        $this->givenFileOperations_isDirectory_returns($this->fileOperations, self::BASE_DIRECTORY, true);

        $cache = new ImageCache(
            self::BASE_DIRECTORY,
            $this->fileOperations,
            $this->imageProcessor,
            $this->imageExtractor,
            $writer
        );

        return $cache;
    }

    private function givenImageExtractor_extract_returnsNull(): void
    {
        \Phake::when($this->imageExtractor)
            ->extract(\Phake::anyParameters())
            ->thenReturn(null);
    }

    private function givenImageExtractor_extract_returnsImage(string $imageKey): Image
    {
        $image = \Phake::mock(Image::class);

        \Phake::when($this->imageExtractor)
            ->extract($imageKey)
            ->thenReturn($image);

        return $image;
    }

    private function givenStream(): StreamInterface
    {
        return \Phake::mock(StreamInterface::class);
    }

    private function assertImageProcessor_saveToFile_isCalledOnceWith(Image $image): void
    {
        \Phake::verify($this->imageProcessor, \Phake::times(1))
            ->saveToFile($image, self::GET_DESTINATION_FILENAME);
    }

}
