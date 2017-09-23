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

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Imaging\Image\ImageBlob;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;

class ImageBlobTest extends TestCase
{
    use ImageTrait, FileOperationsTrait;

    const BLOB = 'raw_image';
    const DESTINATION_FILENAME = '/tmp/file.jpg';

    /** @var SaveOptions */
    private $saveOptions;

    /** @var FileOperationsInterface */
    private $fileOperations;

    protected function setUp()
    {
        $this->saveOptions = \Phake::mock(SaveOptions::class);
        $this->fileOperations = $this->givenFileOperations();
    }

    public function testSaveTo_GivenBlob_DataSavedToFile(): void
    {
        $image = $this->createImage(self::BLOB);

        $image->saveTo(self::DESTINATION_FILENAME);

        $this->assertFileOperations_createFile_isCalledOnce(
            $this->fileOperations,
            self::DESTINATION_FILENAME,
            self::BLOB
        );
    }

    public function testOpen_GivenBlob_ProcessingImageInterfaceIsReturned(): void
    {
        $image = $this->createImage(self::BLOB);
        $expectedProcessingImage = $this->givenProcessingImage();
        $processingEngine = $this->givenProcessingEngine_OpenFromBlob_Returns(self::BLOB, $expectedProcessingImage);

        $processingImage = $image->open($processingEngine);

        $this->assertSame($expectedProcessingImage, $processingImage);
    }

    public function testGetBlob_GivenBlob_BlobIsReturned(): void
    {
        $image = $this->createImage(self::BLOB);

        $blob = $image->getBlob();

        $this->assertEquals(self::BLOB, $blob);
    }

    private function createImage(string $blob): ImageBlob
    {
        $image = new ImageBlob($blob, $this->fileOperations, $this->saveOptions);

        return $image;
    }

    private function givenProcessingEngine_OpenFromBlob_Returns(
        string $data,
        ProcessingImageInterface $processingImage
    ): ProcessingEngineInterface {
        $processingEngine = \Phake::mock(ProcessingEngineInterface::class);

        \Phake::when($processingEngine)
            ->openFromBlob($data, $this->saveOptions)
            ->thenReturn($processingImage);

        return $processingEngine;
    }
}
