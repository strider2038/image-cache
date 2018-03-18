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
use Strider2038\ImgCache\Imaging\Insertion\SourceImageWriter;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class SourceImageWriterTest extends TestCase
{
    use ProviderTrait;

    private const DIRECTORY = 'directory';
    private const FILENAME = 'key';
    private const PARSED_FILENAME_VALUE = 'public_filename';

    /** @var PlainFilenameParserInterface */
    private $filenameParser;
    /** @var StorageAccessorInterface */
    private $storageAccessor;

    protected function setUp(): void
    {
        $this->filenameParser = \Phake::mock(PlainFilenameParserInterface::class);
        $this->storageAccessor = \Phake::mock(StorageAccessorInterface::class);
    }

    /**
     * @test
     * @param bool $expectedExists
     * @dataProvider boolValuesProvider
     */
    public function imageExists_sourceAccessorExistsReturnBool_boolIsReturned(bool $expectedExists): void
    {
        $writer = $this->createSourceImageWriter();
        $publicFilename = self::PARSED_FILENAME_VALUE;
        $this->givenFilenameParser_getParsedFilename_returnsPlainFilename();
        $this->givenStorageAccessor_imageExists_returnsBoolean($publicFilename, $expectedExists);

        $actualExists = $writer->imageExists(self::FILENAME);

        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertEquals($expectedExists, $actualExists);
    }

    /** @test */
    public function insertImage_givenKeyAndData_keyIsParsedAndSourceAccessorPutIsCalled(): void
    {
        $writer = $this->createSourceImageWriter();
        $image = \Phake::mock(Image::class);
        $this->givenFilenameParser_getParsedFilename_returnsPlainFilename();

        $writer->insertImage(self::FILENAME, $image);

        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertStorageAccessor_putImage_isCalledOnceWithImage($image);
    }

    /** @test */
    public function deleteImage_givenKey_keyIsParsedAndSourceAccessorDeleteIsCalled(): void
    {
        $writer = $this->createSourceImageWriter();
        $this->givenFilenameParser_getParsedFilename_returnsPlainFilename();

        $writer->deleteImage(self::FILENAME);

        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertStorageAccessor_deleteImage_isCalledOnceWithParsedFilenameValue();
    }

    /** @test */
    public function getImageFileNameMask_givenKey_keyIsParsedAndFileMaskIsReturned(): void
    {
        $writer = $this->createSourceImageWriter();
        $this->givenFilenameParser_getParsedFilename_returnsPlainFilename();

        $filename = $writer->getImageFileNameMask(self::FILENAME);

        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertEquals(self::PARSED_FILENAME_VALUE, $filename);
    }

    /** @test */
    public function deleteDirectoryContents_givenDirectory_storageAccessorDeletesContentsOfDirectory(): void
    {
        $writer = $this->createSourceImageWriter();

        $writer->deleteDirectoryContents(self::DIRECTORY);

        $this->assertStorageDirectory_deleteDirectoryContents_isCalledOnceWithDirectory(self::DIRECTORY);
    }

    private function givenFilenameParser_getParsedFilename_returnsPlainFilename(): PlainFilename
    {
        $parsedKey = \Phake::mock(PlainFilename::class);
        \Phake::when($this->filenameParser)->getParsedFilename(\Phake::anyParameters())->thenReturn($parsedKey);
        \Phake::when($parsedKey)->getValue()->thenReturn(self::PARSED_FILENAME_VALUE);

        return $parsedKey;
    }

    private function givenStorageAccessor_imageExists_returnsBoolean(string $publicFilename, bool $value): void
    {
        \Phake::when($this->storageAccessor)->imageExists($publicFilename)->thenReturn($value);
    }

    private function assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->filenameParser, \Phake::times(1))->getParsedFilename($filename);
    }

    private function assertStorageAccessor_putImage_isCalledOnceWithImage(Image $image): void
    {
        \Phake::verify($this->storageAccessor, \Phake::times(1))
            ->putImage(self::PARSED_FILENAME_VALUE, $image);
    }

    private function assertStorageAccessor_deleteImage_isCalledOnceWithParsedFilenameValue(): void
    {
        \Phake::verify($this->storageAccessor, \Phake::times(1))
            ->deleteImage(self::PARSED_FILENAME_VALUE);
    }

    private function createSourceImageWriter(): SourceImageWriter
    {
        return new SourceImageWriter($this->filenameParser, $this->storageAccessor);
    }

    private function assertStorageDirectory_deleteDirectoryContents_isCalledOnceWithDirectory(string $directory): void
    {
        \Phake::verify($this->storageAccessor, \Phake::times(1))
            ->deleteDirectoryContents($directory);
    }
}
