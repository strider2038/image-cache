<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Source;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Source\FilesystemSource;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemSourceTest extends TestCase
{
    use FileOperationsTrait;

    private const BASE_DIRECTORY = '/base';
    private const FILENAME_NOT_EXIST = 'not.exist';
    private const FILENAME_EXISTS_FULL = self::BASE_DIRECTORY . '/cat.jpg';
    private const FILENAME_EXISTS = 'cat.jpg';
    private const DATA = 'data';
    private const CHUNK_SIZE = 8 * 1024 * 1024;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var FileOperationsInterface */
    private $fileOperations;
    
    public function setUp() 
    {
        parent::setUp();
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->fileOperations = $this->givenFileOperations();
    }

    /** @test */
    public function construct_baseDirectoryExists_baseDirectoryIsReturned(): void
    {
        $source = $this->createFilesystemSource();

        $this->assertEquals(self::BASE_DIRECTORY . '/', $source->getBaseDirectory());
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Directory .* does not exist/
     */
    public function construct_baseDirectoryDoesNotExist_exceptionThrown(): void
    {
        $this->givenFileOperations_isDirectory_returns($this->fileOperations, self::BASE_DIRECTORY, false);
        new FilesystemSource(self::BASE_DIRECTORY, $this->fileOperations, $this->imageFactory);
    }

    /** @test */
    public function get_fileDoesNotExist_nullIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_NOT_EXIST);

        $image = $source->get($filenameKey);

        $this->assertNull($image);
    }

    /** @test */
    public function get_fileExists_imageIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME_EXISTS_FULL, true);
        $this->givenImageFactory_createFromFile_returnsImage(self::FILENAME_EXISTS);

        $image = $source->get($filenameKey);

        $this->assertInstanceOf(Image::class, $image);
    }

    /** @test */
    public function exists_fileDoesNotExist_falseIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_NOT_EXIST);

        $exists = $source->exists($filenameKey);

        $this->assertFalse($exists);
    }

    /** @test */
    public function exists_fileExists_trueIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME_EXISTS_FULL, true);
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);

        $exists = $source->exists($filenameKey);

        $this->assertTrue($exists);
    }

    /** @test */
    public function put_givenKeyAndStream_directoryCreatedAndStreamIsWrittenToFile(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);
        $givenStream = $this->givenInputStream();
        $stream = $this->givenFileOperations_openFile_returnsStream(
            $this->fileOperations,
            self::FILENAME_EXISTS_FULL,
            'w+'
        );

        $source->put($filenameKey, $givenStream);

        $this->assertFileOperations_createDirectory_isCalledOnce($this->fileOperations, self::BASE_DIRECTORY);
        \Phake::verify($stream, \Phake::times(1))->write(self::DATA);
    }

    /** @test */
    public function delete_givenKey_fileIsDeleted(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);

        $source->delete($filenameKey);

        $this->assertFileOperations_deleteFile_isCalledOnce($this->fileOperations, self::FILENAME_EXISTS_FULL);
    }

    private function createFilesystemSource(string $baseDirectory = self::BASE_DIRECTORY): FilesystemSource
    {
        $this->givenFileOperations_isDirectory_returns($this->fileOperations, self::BASE_DIRECTORY, true);

        $source = new FilesystemSource($baseDirectory, $this->fileOperations, $this->imageFactory);

        return $source;
    }

    private function givenFilenameKey($filename): FilenameKeyInterface
    {
        $filenameKey = \Phake::mock(FilenameKeyInterface::class);

        \Phake::when($filenameKey)->getValue()->thenReturn($filename);

        return $filenameKey;
    }

    private function givenImageFactory_createFromFile_returnsImage($imageFilename): Image
    {
        $image = \Phake::mock(Image::class);

        \Phake::when($this->imageFactory)
            ->createFromFile($imageFilename)
            ->thenReturn($image);

        return $image;
    }

    private function givenInputStream(): StreamInterface
    {
        $givenStream = \Phake::mock(StreamInterface::class);
        \Phake::when($givenStream)->eof()->thenReturn(false)->thenReturn(true);
        \Phake::when($givenStream)->read(self::CHUNK_SIZE)->thenReturn(self::DATA);

        return $givenStream;
    }
}
