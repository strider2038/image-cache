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
use Strider2038\ImgCache\Core\FileOperations;
use Strider2038\ImgCache\Imaging\Extraction\ImageExtractorInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\ImageCache;
use Strider2038\ImgCache\Imaging\Insertion\ImageWriterInterface;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;

class ImageCacheTest extends TestCase
{
    use FileOperationsTrait, ImageTrait;

    const BASE_DIRECTORY = '/cache';

    const INVALID_KEY = 'a';

    const GET_KEY = '/a.jpg';
    const GET_DESTINATION_FILENAME = self::BASE_DIRECTORY . '/a.jpg';

    const INSERT_KEY = '/b.jpg';
    const INSERT_DATA = 'data';

    const DELETE_KEY = '/c.jpg';
    const DELETE_KEY_DESTINATION_FILENAME = self::BASE_DIRECTORY . '/c.jpg';

    const REBUILD_KEY = '/d.jpg';
    const REBUILD_DESTINATION_FILENAME = self::BASE_DIRECTORY . '/d.jpg';

    /** @var FileOperations */
    private $fileOperations;

    /** @var ImageExtractorInterface */
    private $imageExtractor;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->fileOperations = $this->givenFileOperations();
        $this->imageExtractor = \Phake::mock(ImageExtractorInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Directory .* does not exist/
     */
    public function testConstruct_CacheDirectoryIsInvalid_ExceptionThrown(): void
    {
        $this->givenFileOperations_IsDirectory_Returns($this->fileOperations, self::BASE_DIRECTORY, false);

        new ImageCache(
            self::BASE_DIRECTORY,
            $this->fileOperations,
            $this->imageFactory,
            $this->imageExtractor
        );
    }

    public function testGet_ImageDoesNotExistInSource_NullIsReturned(): void
    {
        $cache = $this->createImageCache();
        $this->givenImageExtractor_Extract_ReturnsNull();

        $image = $cache->get(self::GET_KEY);

        $this->assertNull($image);
    }

    public function testGet_ImageExistsInSource_SourceImageSavedToWebDirectoryAndCachedImageIsReturned(): void
    {
        $cache = $this->createImageCache();
        $extractedImage = $this->givenImageExtractor_Extract_ReturnsImage(self::GET_KEY);
        $createdImage = $this->givenImageFactory_CreateImageFile_ReturnsImage($this->imageFactory, self::GET_DESTINATION_FILENAME);

        $image = $cache->get(self::GET_KEY);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertImage_SavedTo_IsCalledOnce($extractedImage, self::GET_DESTINATION_FILENAME);
        $this->assertImageFactory_CreateImageFile_IsCalledOnce($this->imageFactory, self::GET_DESTINATION_FILENAME);
        $this->assertSame($createdImage, $image);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\NotAllowedException
     * @expectedExceptionCode 405
     * @expectedExceptionMessage Operation 'put' is not allowed
     */
    public function testPut_ImageWriterIsNotSpecified_NotAllowedExceptionThrown(): void
    {
        $cache = $this->createImageCache();

        $cache->put(self::INSERT_KEY, self::INSERT_DATA);
    }

    public function testPut_ImageWriterIsSpecified_InsertMethodCalled(): void
    {
        $writer = \Phake::mock(ImageWriterInterface::class);
        $cache = $this->createImageCache($writer);

        $cache->put(self::INSERT_KEY, self::INSERT_DATA);

        \Phake::verify($writer, \Phake::times(1))->insert(self::INSERT_KEY, self::INSERT_DATA);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\NotAllowedException
     * @expectedExceptionCode 405
     * @expectedExceptionMessage Operation 'delete' is not allowed
     */
    public function testDelete_ImageWriterIsNotSpecified_NotAllowedExceptionThrown(): void
    {
        $cache = $this->createImageCache();

        $cache->delete(self::DELETE_KEY);
    }

    public function testDelete_ImageWriterIsSpecified_DeleteMethodCalled(): void
    {
        $writer = \Phake::mock(ImageWriterInterface::class);
        $cache = $this->createImageCache($writer);

        $cache->delete(self::DELETE_KEY);

        \Phake::verify($writer, \Phake::times(1))->delete(self::DELETE_KEY);
        $this->assertFileOperations_DeleteFile_IsCalledOnce($this->fileOperations, self::DELETE_KEY_DESTINATION_FILENAME);
    }

    public function testExists_KeyIsSet_ExistsCalledWithKeyAndValueReturned(): void
    {
        $cache = $this->createImageCache();
        \Phake::when($this->imageExtractor)->exists(self::GET_KEY)->thenReturn(true);

        $result = $cache->exists(self::GET_KEY);

        \Phake::verify($this->imageExtractor, \Phake::times(1))->exists(self::GET_KEY);
        $this->assertTrue($result);
    }

    public function testRebuild_CachedImageExists_ImageRemovedFromCacheAndSavedFromSourceToWebDirectory(): void
    {
        $cache = $this->createImageCacheWithMockedGetMethod();
        $this->givenFileOperations_IsFile_Returns($this->fileOperations, self::REBUILD_DESTINATION_FILENAME, true);

        $cache->rebuild(self::REBUILD_KEY);

        $this->assertFileNotExists(self::REBUILD_DESTINATION_FILENAME);
        $this->assertEquals(self::REBUILD_KEY, $cache->testGetKey);
        $this->assertFileOperations_DeleteFile_IsCalledOnce($this->fileOperations, self::REBUILD_DESTINATION_FILENAME);
    }

    /**
     * @param string $method
     * @param array $params
     * @dataProvider invalidKeyProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidValueException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Key must start with slash
     */
    public function testGivenMethod_GivenInvalidKey_ExceptionThrown(string $method, array $params): void
    {
        $cache = $this->createImageCache();

        call_user_func_array([$cache, $method], $params);
    }

    public function invalidKeyProvider(): array
    {
        return [
            ['get', [self::INVALID_KEY]],
            ['put', [self::INVALID_KEY, '']],
            ['delete', [self::INVALID_KEY]],
            ['exists', [self::INVALID_KEY]],
            ['rebuild', [self::INVALID_KEY]],
        ];
    }

    private function createImageCache(ImageWriterInterface $writer = null): ImageCache
    {
        $this->givenFileOperations_IsDirectory_Returns($this->fileOperations, self::BASE_DIRECTORY, true);

        $cache = new ImageCache(
            self::BASE_DIRECTORY,
            $this->fileOperations,
            $this->imageFactory,
            $this->imageExtractor,
            $writer
        );

        return $cache;
    }

    private function createImageCacheWithMockedGetMethod()
    {
        $dir = self::BASE_DIRECTORY;
        $fileOperations = $this->fileOperations;
        $imageFactory = $this->imageFactory;
        $imageExtractor = $this->imageExtractor;

        $this->givenFileOperations_IsDirectory_Returns($this->fileOperations, self::BASE_DIRECTORY, true);

        $cache = new class ($dir, $fileOperations, $imageFactory, $imageExtractor) extends ImageCache {
            public $testGetKey = null;
            public function get(string $key): ?ImageInterface
            {
                $this->testGetKey = $key;
                return null;
            }
        };

        return $cache;
    }

    private function givenImageExtractor_Extract_ReturnsNull(): void
    {
        \Phake::when($this->imageExtractor)
            ->extract(\Phake::anyParameters())
            ->thenReturn(null);
    }

    private function givenImageExtractor_Extract_ReturnsImage(string $imageKey): ImageInterface
    {
        $image = \Phake::mock(ImageInterface::class);

        \Phake::when($this->imageExtractor)
            ->extract($imageKey)
            ->thenReturn($image);

        return $image;
    }

}
