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
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageFileTest extends TestCase
{
    use ImageTrait, FileOperationsTrait;

    const FILENAME = '/tmp/file.jpg';
    const BLOB = 'blob';
    const COPY_FILENAME = '/tmp/file-copy.jpg';

    /** @var SaveOptions */
    private $saveOptions;

    /** @var FileOperationsInterface */
    private $fileOperations;

    protected function setUp()
    {
        parent::setUp();
        $this->saveOptions = \Phake::mock(SaveOptions::class);
        $this->fileOperations = $this->givenFileOperations();
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found/
     */
    public function testConstruct_FileDoesNotExist_ExceptionThrown(): void
    {
        $this->givenFileOperations_IsFile_Returns($this->fileOperations, self::FILENAME, false);

        $this->createImage();
    }

    public function testConstruct_FileExists_FileNameIsCorrect(): void
    {
        $this->givenFileOperations_IsFile_Returns($this->fileOperations, self::FILENAME, true);

        $image = $this->createImage();

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertEquals(self::FILENAME, $image->getFilename());
    }

    public function testSaveTo_FileExists_FileCopiedToDestination(): void
    {
        $this->givenFileOperations_IsFile_Returns($this->fileOperations, self::FILENAME, true);
        $image = $this->createImage();

        $image->saveTo(self::COPY_FILENAME);

        $this->assertFileOperations_CopyFileTo_IsCalledOnce(
            $this->fileOperations,
            self::FILENAME,
            self::COPY_FILENAME
        );
    }

    public function testOpen_GivenFile_ProcessingImageInterfaceIsReturned(): void
    {
        $this->givenFileOperations_IsFile_Returns($this->fileOperations, self::FILENAME, true);
        $image = $this->createImage();
        $expectedProcessingImage = $this->givenProcessingImage();
        $processingEngine = $this->givenProcessingEngine_OpenFromFile_Returns(self::FILENAME, $expectedProcessingImage);

        $processingImage = $image->open($processingEngine);

        $this->assertSame($expectedProcessingImage, $processingImage);
    }

    public function testGetBlob_GivenFile_BlobIsReturned(): void
    {
        $this->givenFileOperations_IsFile_Returns($this->fileOperations, self::FILENAME, true);
        $image = $this->createImage();
        $this->givenFileOperations_GetFileContents_Returns($this->fileOperations, self::FILENAME, self::BLOB);

        $blob = $image->getBlob();

        $this->assertEquals(self::BLOB, $blob);
    }

    private function createImage(): ImageFile
    {
        $image = new ImageFile(self::FILENAME, $this->fileOperations, $this->saveOptions);

        return $image;
    }

    private function givenProcessingEngine_OpenFromFile_Returns(
        string $filename,
        ProcessingImageInterface $processingImage
    ): ProcessingEngineInterface {
        $processingEngine = \Phake::mock(ProcessingEngineInterface::class);

        \Phake::when($processingEngine)
            ->openFromFile($filename, $this->saveOptions)
            ->thenReturn($processingImage);

        return $processingEngine;
    }

}
