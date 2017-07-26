<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Extraction\Result;

use Strider2038\ImgCache\Imaging\Extraction\Result\FileSourceImage;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class FileSourceImageTest extends FileTestCase
{
    const VALID_DESTINATION_FILENAME = self::TEST_CACHE_DIR . '/valid_destination_file.jpg';

    public function testSaveTo_FileExists_FileCopiedToDestination(): void
    {
        $fileSourceImage = $this->createFileSourceImage();
        $this->assertFileNotExists(self::VALID_DESTINATION_FILENAME);

        $fileSourceImage->saveTo(self::VALID_DESTINATION_FILENAME);

        $this->assertFileExists(self::VALID_DESTINATION_FILENAME);
    }

    public function testOpen_FileExists_ProcessingImageInterfaceIsReturned(): void
    {
        $fileSourceImage = $this->createFileSourceImage();
        $expectedProcessingImage = $this->givenProcessingImage();
        $processingEngine = $this->givenProcessingEngine($expectedProcessingImage);

        $processingImage = $fileSourceImage->open($processingEngine);

        $this->assertSame($expectedProcessingImage, $processingImage);
    }

    private function createFileSourceImage(): FileSourceImage
    {
        $filename = $this->givenFile(self::IMAGE_CAT300);
        $fileSourceImage = new FileSourceImage($filename);

        return $fileSourceImage;
    }

    private function givenProcessingEngine(
        ProcessingImageInterface $processingImage
    ): ProcessingEngineInterface {
        $processingEngine = \Phake::mock(ProcessingEngineInterface::class);

        $filename = $this->givenFile(self::IMAGE_CAT300);

        \Phake::when($processingEngine)
            ->open($filename)
            ->thenReturn($processingImage);

        return $processingEngine;
    }

    private function givenProcessingImage(): ProcessingImageInterface
    {
        $expectedProcessingImage = \Phake::mock(ProcessingImageInterface::class);

        return $expectedProcessingImage;
    }
}
