<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Adapter;

use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\FileOperations;
use Strider2038\ImgCache\Imaging\Processing\Adapter\ImagickImage;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;

class ImagickImageTest extends FileTestCase
{
    use FileOperationsTrait, LoggerTrait;

    const BLOB = 'blob';

    const TEST_IMAGE_FILE = self::TEST_CACHE_DIR . '/imagick.jpg';
    const TEST_IMAGE_DESTINATION_FILE = self::TEST_CACHE_DIR . '/imagick_result.jpg';

    const IMAGE_CAT300_HEIGHT = 200;
    const IMAGE_CAT300_WIDTH = 302;

    const IMAGE_DESTINATION_FILE_QUALITY = 50;

    /** @var SaveOptions */
    private $saveOptions;

    /** @var FileOperations */
    private $fileOperations;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp()
    {
        parent::setUp();
        $this->givenAssetFile(self::IMAGE_CAT300, self::TEST_IMAGE_FILE);
        $this->saveOptions = \Phake::mock(SaveOptions::class);
        $this->fileOperations = $this->givenFileOperations();
        $this->logger = $this->givenLogger();
    }

    public function testGetHeight_GivenImage_HeightReturned(): void
    {
        $image = $this->createImagickImage();

        $height = $image->getHeight();

        $this->assertEquals(self::IMAGE_CAT300_HEIGHT, $height);
    }

    public function testGetWidth_GivenImage_WidthReturned(): void
    {
        $image = $this->createImagickImage();

        $width = $image->getWidth();

        $this->assertEquals(self::IMAGE_CAT300_WIDTH, $width);
    }

    public function testCrop_GivenParams_ImageSizeReduced(): void
    {
        $image = $this->createImagickImage();

        $image->crop(self::IMAGE_CAT300_WIDTH - 1, self::IMAGE_CAT300_HEIGHT - 1, 2, 2);

        $this->assertEquals(self::IMAGE_CAT300_WIDTH - 2, $image->getWidth());
        $this->assertEquals(self::IMAGE_CAT300_HEIGHT - 2, $image->getHeight());
    }

    public function testResize_GivenParams_ImageSizeReduced(): void
    {
        $image = $this->createImagickImage();

        $image->resize(self::IMAGE_CAT300_WIDTH - 10, self::IMAGE_CAT300_HEIGHT - 10);

        $this->assertEquals(self::IMAGE_CAT300_WIDTH - 10, $image->getWidth());
        $this->assertEquals(self::IMAGE_CAT300_HEIGHT - 10, $image->getHeight());
    }

    public function testSaveTo_GivenImage_ImageIsSaveToFile(): void
    {
        $image = $this->createImagickImage();
        $this->givenFileOperations_IsDirectory_ReturnsFalse();
        $this->givenSaveOptions_GetQuality_Returns(self::IMAGE_DESTINATION_FILE_QUALITY);

        $image->saveTo(self::TEST_IMAGE_DESTINATION_FILE);

        $this->assertFileExists(self::TEST_IMAGE_DESTINATION_FILE);
        $this->assertFileOperations_CreateDirectory_IsCalledOnce();
        $this->assertLogger_Info_IsCalledOnce($this->logger);
    }

    public function testOpen_GivenImage_ProcessingImageIsReturned(): void
    {
        $imageFilename = $this->givenAssetFile(self::IMAGE_BOX_PNG);
        $image = $this->createImagickImage($imageFilename);
        $engine = $this->givenProcessingEngine();

        $processingImage = $image->open($engine);

        $this->assertInstanceOf(ProcessingImageInterface::class, $processingImage);
    }

    public function testGetBlob_GivenImagick_GetImageBlobIsReturned(): void
    {
        $imageFilename = $this->givenAssetFile(self::IMAGE_BOX_PNG);
        $imagick = new \Imagick($imageFilename);
        $image = new ImagickImage($imagick, $this->fileOperations, $this->saveOptions);

        $blob = $image->getBlob();

        $this->assertSame($imagick->getImageBlob(), $blob);
    }

    private function createImagickImage(string $filename = self::TEST_IMAGE_FILE): ImagickImage
    {
        $imagick = new \Imagick($filename);
        $image = new ImagickImage($imagick, $this->fileOperations, $this->saveOptions);

        $image->setLogger($this->logger);

        return $image;
    }

    private function givenProcessingEngine(): ProcessingEngineInterface
    {
        $processingEngine = \Phake::mock(ProcessingEngineInterface::class);
        $processingImage = \Phake::mock(ProcessingImageInterface::class);

        \Phake::when($processingEngine)
            ->openFromBlob(\Phake::anyParameters())
            ->thenReturn($processingImage);

        return $processingEngine;
    }

    private function givenFileOperations_IsDirectory_ReturnsFalse(): void
    {
        \Phake::when($this->fileOperations)->isDirectory(self::TEST_CACHE_DIR)->thenReturn(false);
    }

    private function assertFileOperations_CreateDirectory_IsCalledOnce(): void
    {
        \Phake::verify($this->fileOperations, \Phake::times(1))
            ->createDirectory(self::TEST_CACHE_DIR);
    }

    private function givenSaveOptions_GetQuality_Returns(int $quality): void
    {
        \Phake::when($this->saveOptions)->getQuality()->thenReturn($quality);
    }
}
