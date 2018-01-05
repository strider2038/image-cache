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
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactory;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Imaging\Image\ImageParametersFactoryInterface;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;

class ImageFactoryTest extends TestCase
{
    use FileOperationsTrait;

    private const FILENAME = 'file';
    private const DATA = 'data';

    /** @var ImageParametersFactoryInterface */
    private $imageParametersFactory;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->imageParametersFactory = \Phake::mock(ImageParametersFactoryInterface::class);
        $this->imageValidator = \Phake::mock(ImageValidatorInterface::class);
        $this->fileOperations = $this->givenFileOperations();
        $this->streamFactory = \Phake::mock(StreamFactoryInterface::class);
    }

    /** @test */
    public function createImageFromFile_givenImage_imageIsReturned(): void
    {
        $factory = $this->createImageFactory();
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME, true);
        $this->givenImageValidator_hasValidImageExtension_returns(true);
        $this->givenImageValidator_hasFileValidImageMimeType_returns(true);
        $imageParameters = $this->givenImageParametersFactory_createImageParameters_returnsImageParameters();
        $expectedStream = $this->givenFileOperations_openFile_returnsStream($this->fileOperations);

        $image = $factory->createImageFromFile(self::FILENAME);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertSame($imageParameters, $image->getParameters());
        $this->assertSame($expectedStream, $image->getData());
        $this->assertFileOperations_openFile_isCalledOnceWithFilenameAndMode(
            $this->fileOperations,
            self::FILENAME,
            ResourceStreamModeEnum::READ_ONLY
        );
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found/
     */
    public function createImageFromFile_givenFileDoesNotExist_exceptionThrown(): void
    {
        $factory = $this->createImageFactory();
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME, false);

        $factory->createImageFromFile(self::FILENAME);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidMediaTypeException
     * @expectedExceptionCode 415
     * @expectedExceptionMessageRegExp /File .* has unsupported image extension/
     */
    public function createImageFromFile_givenImageHasInvalidExtension_exceptionThrown(): void
    {
        $factory = $this->createImageFactory();
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME, true);
        $this->givenImageValidator_hasValidImageExtension_returns(false);

        $factory->createImageFromFile(self::FILENAME);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidMediaTypeException
     * @expectedExceptionCode 415
     * @expectedExceptionMessageRegExp /File .* has unsupported mime type/
     */
    public function createImageFromFile_givenImageHasInvalidMimeType_exceptionThrown(): void
    {
        $factory = $this->createImageFactory();
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME, true);
        $this->givenImageValidator_hasValidImageExtension_returns(true);
        $this->givenImageValidator_hasFileValidImageMimeType_returns(false);

        $factory->createImageFromFile(self::FILENAME);
    }

    /** @test */
    public function createImageFromData_givenBlob_imageIsReturned(): void
    {
        $factory = $this->createImageFactory();
        $this->givenImageValidator_hasDataValidImageMimeType_returns(true);
        $imageParameters = $this->givenImageParametersFactory_createImageParameters_returnsImageParameters();
        $stream = $this->givenStreamFactory_createStreamFromData_returnsStream();

        $image = $factory->createImageFromData(self::DATA);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertSame($imageParameters, $image->getParameters());
        $this->assertStreamFactory_createStreamFromData_isCalledOnceWithData(self::DATA);
        $this->assertSame($stream, $image->getData());
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidMediaTypeException
     * @expectedExceptionCode 415
     * @expectedExceptionMessage Image has unsupported mime type
     */
    public function createImageFromData_givenImageHasInvalidMimeType_exceptionThrown(): void
    {
        $factory = $this->createImageFactory();
        $this->givenImageValidator_hasDataValidImageMimeType_returns(false);

        $factory->createImageFromData(self::DATA);
    }

    /** @test */
    public function createImageFromStream_givenStreamAndNoParameters_imageWithCreatedParametersReturned(): void
    {
        $factory = $this->createImageFactory();
        $stream = $this->givenStreamWithData();
        $this->givenImageValidator_hasDataValidImageMimeType_returns(true);
        $imageParameters = $this->givenImageParametersFactory_createImageParameters_returnsImageParameters();

        $image = $factory->createImageFromStream($stream);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertStream_rewind_isCalledOnce($stream);
        $this->assertImageParametersFactory_createImageParameters_isCalledOnce();
        $this->assertSame($imageParameters, $image->getParameters());
        $this->assertSame($stream, $image->getData());
        $this->assertImageValidator_hasDataValidImageMimeType_isCalledOnceWith(self::DATA);
    }

    /** @test */
    public function createImageFromStream_givenStreamAndParameters_imageWithGivenParametersReturned(): void
    {
        $factory = $this->createImageFactory();
        $stream = $this->givenStreamWithData();
        $this->givenImageValidator_hasDataValidImageMimeType_returns(true);
        $imageParameters = \Phake::mock(ImageParameters::class);

        $image = $factory->createImageFromStream($stream, $imageParameters);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertStream_rewind_isCalledOnce($stream);
        $this->assertImageParametersFactory_createImageParameters_isNeverCalled();
        $this->assertSame($imageParameters, $image->getParameters());
        $this->assertSame($stream, $image->getData());
        $this->assertImageValidator_hasDataValidImageMimeType_isCalledOnceWith(self::DATA);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidMediaTypeException
     * @expectedExceptionCode 415
     * @expectedExceptionMessage Image has unsupported mime type
     */
    public function createImageFromStream_givenImageHasInvalidMimeType_exceptionThrown(): void
    {
        $factory = $this->createImageFactory();
        $stream = $this->givenStreamWithData();
        $this->givenImageValidator_hasDataValidImageMimeType_returns(false);

        $factory->createImageFromStream($stream);
    }

    private function createImageFactory(): ImageFactory
    {
        $factory = new ImageFactory(
            $this->imageParametersFactory,
            $this->imageValidator,
            $this->fileOperations,
            $this->streamFactory
        );

        return $factory;
    }

    private function givenImageParametersFactory_createImageParameters_returnsImageParameters(): ImageParameters
    {
        $imageParameters = \Phake::mock(ImageParameters::class);
        \Phake::when($this->imageParametersFactory)->createImageParameters()->thenReturn($imageParameters);

        return $imageParameters;
    }

    private function assertImageParametersFactory_createImageParameters_isCalledOnce(): void
    {
        \Phake::verify($this->imageParametersFactory, \Phake::times(1))->createImageParameters();
    }

    private function assertImageParametersFactory_createImageParameters_isNeverCalled(): void
    {
        \Phake::verify($this->imageParametersFactory, \Phake::times(0))->createImageParameters();
    }

    private function givenImageValidator_hasValidImageExtension_returns(bool $value): void
    {
        \Phake::when($this->imageValidator)
            ->hasValidImageExtension(\Phake::anyParameters())
            ->thenReturn($value);
    }

    private function givenImageValidator_hasFileValidImageMimeType_returns(bool $value): void
    {
        \Phake::when($this->imageValidator)
            ->hasFileValidImageMimeType(\Phake::anyParameters())
            ->thenReturn($value);
    }

    private function givenImageValidator_hasDataValidImageMimeType_returns(bool $value): void
    {
        \Phake::when($this->imageValidator)
            ->hasDataValidImageMimeType(\Phake::anyParameters())
            ->thenReturn($value);
    }

    private function assertImageValidator_hasDataValidImageMimeType_isCalledOnceWith(string $data): void
    {
        \Phake::verify($this->imageValidator, \Phake::times(1))
            ->hasDataValidImageMimeType($data);
    }

    private function givenStreamWithData(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($stream)->getContents()->thenReturn(self::DATA);

        return $stream;
    }

    private function assertStreamFactory_createStreamFromData_isCalledOnceWithData(string $data): void
    {
        \Phake::verify($this->streamFactory, \Phake::times(1))->createStreamFromData($data);
    }

    private function givenStreamFactory_createStreamFromData_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->streamFactory)->createStreamFromData(\Phake::anyParameters())->thenReturn($stream);

        return $stream;
    }

    private function assertStream_rewind_isCalledOnce(StreamInterface $stream): void
    {
        \Phake::verify($stream, \Phake::times(1))->rewind();
    }
}
