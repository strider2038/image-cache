<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Imagick;

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Imaging\Processing\Imagick\ImagickTransformer;
use Strider2038\ImgCache\Imaging\Processing\Rectangle;
use Strider2038\ImgCache\Imaging\Processing\Size;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ImagickTransformerTest extends FileTestCase
{
    private const IMAGE_SOURCE_HEIGHT = 4;
    private const IMAGE_SOURCE_WIDTH = 4;
    private const IMAGE_FINAL_WIDTH = 8;
    private const IMAGE_FINAL_HEIGHT = 8;
    private const QUALITY = 70;
    private const IMAGE_DESTINATION_FILE = self::TEST_CACHE_DIR . '/imagick_result.jpg';

    /** @var \Imagick */
    private $imagick;

    /** @var FileOperationsInterface */
    private $fileOperations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->imagick = new \Imagick($this->givenAssetFile(self::IMAGE_BOX_JPG));
        $this->fileOperations = \Phake::mock(FileOperationsInterface::class);
    }

    /** @test */
    public function resize_givenSize_imageIsResize(): void
    {
        $transformer = $this->createTransformer();
        $size = new Size(self::IMAGE_FINAL_WIDTH, self::IMAGE_FINAL_HEIGHT);

        $returnedTransformer = $transformer->resize($size);

        $this->assertSame($returnedTransformer, $transformer);
        $finalSize = $transformer->getSize();
        $this->assertEquals($size->getHeight(), $finalSize->getHeight());
        $this->assertEquals($size->getWidth(), $finalSize->getWidth());
    }

    /** @test */
    public function crop_givenSize_imageIsCropped(): void
    {
        $transformer = $this->createTransformer();
        $rectangle = new Rectangle(self::IMAGE_SOURCE_WIDTH - 2, self::IMAGE_SOURCE_HEIGHT - 1, 1, 1);

        $returnedTransformer = $transformer->crop($rectangle);

        $this->assertSame($returnedTransformer, $transformer);
        $size = $transformer->getSize();
        $this->assertEquals(2, $size->getWidth());
        $this->assertEquals(3, $size->getHeight());
    }

    /** @test */
    public function getData_givenImage_imageIsReturnedWithTheSameContents(): void
    {
        $transformer = $this->createTransformer();
        $expectedData = $this->imagick->getImageBlob();

        $data = $transformer->getData();

        $this->assertSame($expectedData, $data->getContents());
    }

    /** @test */
    public function setCompressionQuality_givenQuality_valueIsSet(): void
    {
        $transformer = $this->createTransformer();

        $returnedTransformer = $transformer->setCompressionQuality(self::QUALITY);

        $this->assertSame($returnedTransformer, $transformer);
        $this->assertEquals(self::QUALITY, $transformer->getImagick()->getCompressionQuality());
    }

    /** @test */
    public function writeToFile_givenFilename_fileIsCreated(): void
    {
        $transformer = $this->createTransformer();

        $returnedTransformer = $transformer->writeToFile(self::IMAGE_DESTINATION_FILE);

        $this->assertSame($returnedTransformer, $transformer);
        $this->assertFileOperations_createDirectory_isCalledOnce();
        $this->assertFileExists(self::IMAGE_DESTINATION_FILE);
    }

    private function createTransformer(): ImagickTransformer
    {
        return new ImagickTransformer($this->imagick, $this->fileOperations);
    }

    private function assertFileOperations_createDirectory_isCalledOnce(): void
    {
        \Phake::verify($this->fileOperations, \Phake::times(1))->createDirectory(self::TEST_CACHE_DIR);
    }
}
