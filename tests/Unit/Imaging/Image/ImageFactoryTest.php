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
use Strider2038\ImgCache\Core\FileOperations;
use Strider2038\ImgCache\Imaging\Image\ImageBlob;
use Strider2038\ImgCache\Imaging\Image\ImageFactory;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Processing\SaveOptionsFactoryInterface;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;

class ImageFactoryTest extends TestCase
{
    use FileOperationsTrait;

    const FILENAME = 'file';
    const BLOB = 'blob';

    /** @var SaveOptionsFactoryInterface */
    private $saveOptionsFactory;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var FileOperations */
    private $fileOperations;

    protected function setUp()
    {
        parent::setUp();
        $this->saveOptionsFactory = \Phake::mock(SaveOptionsFactoryInterface::class);
        $this->imageValidator = \Phake::mock(ImageValidatorInterface::class);
        $this->fileOperations = $this->givenFileOperations();
    }

    public function testCreateImageFile_GivenImage_ImageFileIsReturned(): void
    {
        $factory = $this->createImageFactory();
        $this->givenFileOperations_IsFile_Returns($this->fileOperations, self::FILENAME, true);
        $this->givenImageValidator_HasValidImageExtension_Returns(true);
        $this->givenImageValidator_HasFileValidImageMimeType_Returns(true);
        $saveOptions = $this->givenSaveOptionsFactory_Create_ReturnsSaveOptions();

        $image = $factory->createImageFile(self::FILENAME);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertSame($saveOptions, $image->getSaveOptions());
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidMediaTypeException
     * @expectedExceptionCode 415
     * @expectedExceptionMessageRegExp /File .* has unsupported image extension/
     */
    public function testCreateImageFile_GivenImageHasInvalidExtension_ExceptionThrown(): void
    {
        $factory = $this->createImageFactory();
        $this->givenImageValidator_HasValidImageExtension_Returns(false);

        $factory->createImageFile(self::FILENAME);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidMediaTypeException
     * @expectedExceptionCode 415
     * @expectedExceptionMessageRegExp /File .* has unsupported mime type/
     */
    public function testCreateImageFile_GivenImageHasInvalidMimeType_ExceptionThrown(): void
    {
        $factory = $this->createImageFactory();
        $this->givenImageValidator_HasValidImageExtension_Returns(true);
        $this->givenImageValidator_HasFileValidImageMimeType_Returns(false);

        $factory->createImageFile(self::FILENAME);
    }

    public function testCreateImageBlob_GivenBlob_ImageBlobIsReturned(): void
    {
        $factory = $this->createImageFactory();
        $this->givenImageValidator_HasBlobValidImageMimeType_Returns(true);
        $saveOptions = $this->givenSaveOptionsFactory_Create_ReturnsSaveOptions();

        $image = $factory->createImageBlob(self::BLOB);

        $this->assertInstanceOf(ImageBlob::class, $image);
        $this->assertSame($saveOptions, $image->getSaveOptions());
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidMediaTypeException
     * @expectedExceptionCode 415
     * @expectedExceptionMessage Image has unsupported mime type
     */
    public function testCreateImageBlob_GivenImageHasInvalidMimeType_ExceptionThrown(): void
    {
        $factory = $this->createImageFactory();
        $this->givenImageValidator_HasBlobValidImageMimeType_Returns(false);

        $factory->createImageBlob(self::BLOB);
    }

    private function createImageFactory(): ImageFactory
    {
        $factory = new ImageFactory(
            $this->saveOptionsFactory,
            $this->imageValidator,
            $this->fileOperations
        );

        return $factory;
    }

    private function givenSaveOptionsFactory_Create_ReturnsSaveOptions(): SaveOptions
    {
        $saveOptions = \Phake::mock(SaveOptions::class);

        \Phake::when($this->saveOptionsFactory)->create()->thenReturn($saveOptions);

        return $saveOptions;
    }

    private function givenImageValidator_HasValidImageExtension_Returns(bool $value): void
    {
        \Phake::when($this->imageValidator)
            ->hasValidImageExtension(\Phake::anyParameters())
            ->thenReturn($value);
    }

    private function givenImageValidator_HasFileValidImageMimeType_Returns(bool $value): void
    {
        \Phake::when($this->imageValidator)
            ->hasFileValidImageMimeType(\Phake::anyParameters())
            ->thenReturn($value);
    }

    private function givenImageValidator_HasBlobValidImageMimeType_Returns(bool $value): void
    {
        \Phake::when($this->imageValidator)
            ->hasBlobValidImageMimeType(\Phake::anyParameters())
            ->thenReturn($value);
    }
}
