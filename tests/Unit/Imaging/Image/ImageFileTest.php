<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging\Image;

use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Tests\Support\{
    FileTestCase, Phake\ImageTrait, TestImages
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageFileTest extends FileTestCase
{
    use ImageTrait;

    const VALID_DESTINATION_FILENAME = self::TEST_CACHE_DIR . '/valid_destination_file.jpg';

    /** @var SaveOptions */
    private $saveOptions;

    protected function setUp()
    {
        parent::setUp();
        $this->saveOptions = \Phake::mock(SaveOptions::class);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found/
     */
    public function testConstruct_FileDoesNotExist_ExceptionThrown(): void
    {
        $this->createImage(self::TEST_CACHE_DIR . '/not.existing');
    }

    public function testConstruct_FileExists_FileNameIsCorrect(): void
    {
        $filename = $this->givenFile(self::IMAGE_CAT300);

        $image = $this->createImage($filename);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertEquals($filename, $image->getFilename());
    }

    public function testSaveTo_FileExists_FileCopiedToDestination(): void
    {
        $image = $this->createImage($this->givenFile(self::IMAGE_CAT300));
        $this->assertFileNotExists(self::VALID_DESTINATION_FILENAME);

        $image->saveTo(self::VALID_DESTINATION_FILENAME);

        $this->assertFileExists(self::VALID_DESTINATION_FILENAME);
    }

    public function testOpen_FileExists_ProcessingImageInterfaceIsReturned(): void
    {
        $filename = $this->givenFile(self::IMAGE_CAT300);
        $image = $this->createImage($filename);
        $expectedProcessingImage = $this->givenProcessingEngine_Open_ReturnsProcessingImage();
        $processingEngine = $this->givenProcessingEngine_OpenFromFile_Returns($filename, $expectedProcessingImage);

        $processingImage = $image->open($processingEngine);

        $this->assertSame($expectedProcessingImage, $processingImage);
    }

    private function createImage(string $filename): ImageFile
    {
        $image = new ImageFile($filename, $this->saveOptions);

        return $image;
    }

    private function givenProcessingEngine_OpenFromFile_Returns(
        string $filename,
        ProcessingImageInterface $processingImage
    ): ProcessingEngineInterface {
        $processingEngine = \Phake::mock(ProcessingEngineInterface::class);

        \Phake::when($processingEngine)
            ->openFromFile($filename)
            ->thenReturn($processingImage);

        return $processingEngine;
    }

}
