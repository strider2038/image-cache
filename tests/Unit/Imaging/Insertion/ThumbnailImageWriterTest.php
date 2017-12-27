<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Insertion;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Insertion\ThumbnailImageWriter;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ThumbnailImageWriterTest extends TestCase
{
    use ProviderTrait;

    private const FILENAME = 'key';
    private const PARSED_FILENAME_VALUE = 'public_filename';
    private const PARSED_FILENAME_MASK = 'thumbnail_mask';

    /** @var ThumbnailFilenameParserInterface */
    private $filenameParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    protected function setUp(): void
    {
        $this->filenameParser = \Phake::mock(ThumbnailFilenameParserInterface::class);
        $this->storageAccessor = \Phake::mock(StorageAccessorInterface::class);
    }

    /**
     * @test
     * @param bool $expectedExists
     * @dataProvider boolValuesProvider
     */
    public function imageExists_storageAccessorExistsReturnsBool_boolIsReturned(bool $expectedExists): void
    {
        $writer = $this->createThumbnailImageWriter();
        $this->givenFilenameParser_getParsedFilename_returnsThumbnailFilename();
        $this->givenStorageAccessor_imageExists_returns($expectedExists);

        $actualExists = $writer->imageExists(self::FILENAME);

        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertEquals($expectedExists, $actualExists);
    }

    /** @test */
    public function insertImage_givenFilenameAndData_filenameIsParsedAndSourceAccessorPutIsCalled(): void
    {
        $writer = $this->createThumbnailImageWriter();
        $image = \Phake::mock(Image::class);
        $this->givenFilenameParser_getParsedFilename_returnsThumbnailFilename();

        $writer->insertImage(self::FILENAME, $image);

        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertStorageAccessor_putImage_isCalledOnceWithFilenameAndImage(self::PARSED_FILENAME_VALUE, $image);
    }

    /** @test */
    public function deleteImage_givenFilename_filenameIsParsedAndSourceAccessorDeleteIsCalled(): void
    {
        $writer = $this->createThumbnailImageWriter();
        $this->givenFilenameParser_getParsedFilename_returnsThumbnailFilename();

        $writer->deleteImage(self::FILENAME);

        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertStorageAccessor_deleteImage_isCalledOnce(self::PARSED_FILENAME_VALUE);
    }

    /** @test */
    public function getImageFileNameMask_givenFilename_filenameIsParsedAndThumbnailMaskIsReturned(): void
    {
        $writer = $this->createThumbnailImageWriter();
        $this->givenFilenameParser_getParsedFilename_returnsThumbnailFilename();

        $filename = $writer->getImageFileNameMask(self::FILENAME);

        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertEquals(self::PARSED_FILENAME_MASK, $filename);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp /Image name .* for source image cannot have process configuration/
     * @param string $method
     * @param array $parameters
     * @dataProvider methodAndParametersProvider
     */
    public function method_givenFilenameHasProcessingConfiguration_exceptionThrown(
        string $method,
        array $parameters
    ): void {
        $writer = $this->createThumbnailImageWriter();
        $this->givenFilenameParser_getParsedFilename_returnsThumbnailFilename(true);

        call_user_func_array([$writer, $method], $parameters);
    }

    public function methodAndParametersProvider(): array
    {
        return [
            ['imageExists', [self::FILENAME]],
            ['insertImage', [self::FILENAME, \Phake::mock(Image::class)]],
            ['deleteImage', [self::FILENAME]],
            ['getImageFileNameMask', [self::FILENAME]],
        ];
    }

    private function givenFilenameParser_getParsedFilename_returnsThumbnailFilename(
        bool $hasProcessingConfiguration = false
    ): ThumbnailFilename {
        $parsedFilename = \Phake::mock(ThumbnailFilename::class);
        \Phake::when($this->filenameParser)->getParsedFilename(self::FILENAME)->thenReturn($parsedFilename);
        \Phake::when($parsedFilename)->getValue()->thenReturn(self::PARSED_FILENAME_VALUE);
        \Phake::when($parsedFilename)->getMask()->thenReturn(self::PARSED_FILENAME_MASK);
        \Phake::when($parsedFilename)->hasProcessingConfiguration()->thenReturn($hasProcessingConfiguration);

        return $parsedFilename;
    }

    private function givenStorageAccessor_imageExists_returns(bool $value): void
    {
        \Phake::when($this->storageAccessor)->imageExists(self::PARSED_FILENAME_VALUE)->thenReturn($value);
    }

    private function assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->filenameParser, \Phake::times(1))->getParsedFilename($filename);
    }

    private function assertStorageAccessor_putImage_isCalledOnceWithFilenameAndImage(
        string $filename,
        Image $image
    ): void {
        \Phake::verify($this->storageAccessor, \Phake::times(1))->putImage($filename, $image);
    }

    private function assertStorageAccessor_deleteImage_isCalledOnce(string $filename): void
    {
        \Phake::verify($this->storageAccessor, \Phake::times(1))->deleteImage($filename);
    }

    private function createThumbnailImageWriter(): ThumbnailImageWriter
    {
        return new ThumbnailImageWriter($this->filenameParser, $this->storageAccessor);
    }
}
