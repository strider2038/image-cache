<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Image;

use Strider2038\ImgCache\Imaging\Image\ImageBlob;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;

class ImageBlobTest extends FileTestCase
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

    public function testSaveTo_FileExists_FileCopiedToDestination(): void
    {
        $filename = $this->givenFile(self::IMAGE_CAT300);
        $image = $this->createImage(file_get_contents($filename));

        $image->saveTo(self::VALID_DESTINATION_FILENAME);

        $this->assertFileExists(self::VALID_DESTINATION_FILENAME);
    }

    public function testOpen_FileExists_ProcessingImageInterfaceIsReturned(): void
    {
        $filename = $this->givenFile(self::IMAGE_CAT300);
        $blob = file_get_contents($filename);
        $image = $this->createImage($blob);
        $expectedProcessingImage = $this->givenProcessingImage();
        $processingEngine = $this->givenProcessingEngine_OpenFromBlob_Returns($blob, $expectedProcessingImage);

        $processingImage = $image->open($processingEngine);

        $this->assertSame($expectedProcessingImage, $processingImage);
    }

    /**
     * @runInSeparateProcess
     * @group separate
     */
    public function testRender_GivenFile_ContentsIsEchoed(): void
    {
        $filename = $this->givenFile(self::IMAGE_BOX_PNG);
        $blob = file_get_contents($filename);
        $image = $this->createImage($blob);
        $this->expectOutputString($blob);

        $image->render();
    }

    private function createImage(string $blob): ImageBlob
    {
        $image = new ImageBlob($blob, $this->saveOptions);

        return $image;
    }

    private function givenProcessingEngine_OpenFromBlob_Returns(
        string $data,
        ProcessingImageInterface $processingImage
    ): ProcessingEngineInterface {
        $processingEngine = \Phake::mock(ProcessingEngineInterface::class);

        \Phake::when($processingEngine)
            ->openFromBlob($data)
            ->thenReturn($processingImage);

        return $processingEngine;
    }
}
