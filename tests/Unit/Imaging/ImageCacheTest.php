<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging;

use Strider2038\ImgCache\Imaging\Extraction\ImageExtractorInterface;
use Strider2038\ImgCache\Imaging\Extraction\Result\ExtractedImageInterface;
use Strider2038\ImgCache\Imaging\Image;
use Strider2038\ImgCache\Imaging\ImageCache;
use Strider2038\ImgCache\Imaging\Insertion\ImageWriterInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ImageCacheTest extends FileTestCase
{
    const GET_KEY = 'a.jpg';
    const INSERT_KEY = 'a';
    const INSERT_DATA = 'data';

    /** @var ImageExtractorInterface */
    private $imageExtractor;

    public function testGet_ImageDoesNotExistInSource_NullIsReturned(): void
    {
        $cache = new ImageCache(self::TEST_CACHE_DIR, $this->imageExtractor);
        \Phake::when($this->imageExtractor)->extract(\Phake::anyParameters())->thenReturn(null);

        $image = $cache->get(self::GET_KEY);

        $this->assertNull($image);
    }

    public function testGet_ImageExistsInSource_SourceImageSavedToWebDirectoryAndCachedImageIsReturned(): void
    {
        $imageKey = '/' . self::IMAGE_CAT300;
        $imageFilename = self::TEST_CACHE_DIR . $imageKey;
        copy($this->givenFile(self::IMAGE_CAT300), $imageFilename);
        $cache = new ImageCache(self::TEST_CACHE_DIR, $this->imageExtractor);
        $extractedImage = \Phake::mock(ExtractedImageInterface::class);
        \Phake::when($this->imageExtractor)->extract($imageKey)->thenReturn($extractedImage);

        $image = $cache->get($imageKey);

        $this->assertInstanceOf(Image::class, $image);
        \Phake::verify($extractedImage, \Phake::times(1))->saveTo($imageFilename);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\NotAllowedException
     * @expectedExceptionCode 405
     * @expectedExceptionMessage Operation 'put' is not allowed
     */
    public function testPut_ImageWriterIsNotSpecified_NotAllowedExceptionThrown(): void
    {
        $cache = new ImageCache(self::TEST_CACHE_DIR, $this->imageExtractor);

        $cache->put(self::INSERT_KEY, self::INSERT_DATA);
    }

    public function testPut_ImageWriterIsSpecified_InsertMethodCalled(): void
    {
        $writer = \Phake::mock(ImageWriterInterface::class);
        $cache = new ImageCache(self::TEST_CACHE_DIR, $this->imageExtractor, $writer);

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
        $cache = new ImageCache(self::TEST_CACHE_DIR, $this->imageExtractor);

        $cache->delete('key');
    }

    public function testDelete_ImageWriterIsSpecified_DeleteMethodCalled(): void
    {
        $imageKey = '/' . self::IMAGE_CAT300;
        $imageFilename = self::TEST_CACHE_DIR . $imageKey;
        copy($this->givenFile(self::IMAGE_CAT300), $imageFilename);
        $writer = \Phake::mock(ImageWriterInterface::class);
        $cache = new ImageCache(self::TEST_CACHE_DIR, $this->imageExtractor, $writer);

        $this->assertFileExists($imageFilename);
        $cache->delete($imageKey);

        \Phake::verify($writer, \Phake::times(1))->delete($imageKey);
        $this->assertFileNotExists($imageFilename);
    }

    public function testExists_KeyIsSet_ExistsCalledWithKeyAndValueReturned(): void
    {
        $cache = new ImageCache(self::TEST_CACHE_DIR, $this->imageExtractor);
        \Phake::when($this->imageExtractor)->exists(self::GET_KEY)->thenReturn(true);

        $result = $cache->exists(self::GET_KEY);

        \Phake::verify($this->imageExtractor, \Phake::times(1))->exists(self::GET_KEY);
        $this->assertTrue($result);
    }

    public function testRebuild_CachedImageExists_ImageRemovedFromCacheAndSavedFromSourceToWebDirectory(): void
    {
        $testFilename = $this->givenFile(self::IMAGE_CAT300);
        [$imageKey, $imageFilename, $cache, $extractedImage] = $this->getImageFileNameForRebuild($testFilename);
        copy($testFilename, $imageFilename);

        $this->assertFileExists($imageFilename);
        $cache->rebuild($imageKey);

        $this->assertFileExists($imageFilename);
        $this->assertTrue($extractedImage->called);
    }

    /**
     * @param $testFilename
     * @return array
     */
    private function getImageFileNameForRebuild($testFilename): array
    {
        $imageKey = '/' . self::IMAGE_CAT300;
        $imageFilename = self::TEST_CACHE_DIR . $imageKey;
        $cache = new ImageCache(self::TEST_CACHE_DIR, $this->imageExtractor);
        $extractedImage = new class implements ExtractedImageInterface
        {
            public $sourceFilename;
            public $called = false;

            public function saveTo(string $filename): void
            {
                $this->called = true;
                copy($this->sourceFilename, $filename);
            }

            public function setSaveOptions(SaveOptions $saveOptions): void {}

            public function open(ProcessingEngineInterface $engine): ProcessingImageInterface {}

        };
        $extractedImage->sourceFilename = $testFilename;
        $extractedImage->testCase = $this;
        \Phake::when($this->imageExtractor)->extract($imageKey)->thenReturn($extractedImage);
        return [$imageKey, $imageFilename, $cache, $extractedImage];
    }

    public function testRebuild_CachedImageNotExists_SourceImageSavedToWebDirectory(): void
    {
        $testFilename = $this->givenFile(self::IMAGE_CAT300);
        [$imageKey, $imageFilename, $cache, $extractedImage] = $this->getImageFileNameForRebuild($testFilename);

        $this->assertFileNotExists($imageFilename);
        $cache->rebuild($imageKey);

        $this->assertFileExists($imageFilename);
        $this->assertTrue($extractedImage->called);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->imageExtractor = \Phake::mock(ImageExtractorInterface::class);
    }
}
