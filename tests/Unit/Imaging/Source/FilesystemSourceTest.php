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
use Strider2038\ImgCache\Core\FileOperations;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Source\FilesystemSource;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemSourceTest extends TestCase
{
    use FileOperationsTrait;

    const BASE_DIRECTORY = '/base';
    const FILENAME_NOT_EXIST = 'not.exist';
    const FILENAME_EXISTS_FULL = self::BASE_DIRECTORY . '/cat.jpg';
    const FILENAME_EXISTS = 'cat.jpg';

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var FileOperations */
    private $fileOperations;
    
    public function setUp() 
    {
        parent::setUp();
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->fileOperations = $this->givenFileOperations();
    }
    
    public function testConstruct_BaseDirectoryExists_BaseDirectoryIsReturned(): void
    {
        $source = $this->createFilesystemSource();

        $this->assertEquals(self::BASE_DIRECTORY . '/', $source->getBaseDirectory());
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Directory .* does not exist/
     */
    public function testConstruct_BaseDirectoryDoesNotExist_ExceptionThrown(): void
    {
        $this->givenFileOperations_IsDirectory_Returns($this->fileOperations, self::BASE_DIRECTORY, false);
        new FilesystemSource(self::BASE_DIRECTORY, $this->fileOperations, $this->imageFactory);
    }
    
    public function testGet_FileDoesNotExist_NullIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_NOT_EXIST);

        $image = $source->get($filenameKey);

        $this->assertNull($image);
    }

    public function testGet_FileExists_ImageIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);
        $this->givenFileOperations_IsFile_Returns($this->fileOperations, self::FILENAME_EXISTS_FULL, true);
        $this->givenImageFactory_CreateImageFile_ReturnsImageFile(self::FILENAME_EXISTS);

        $image = $source->get($filenameKey);

        $this->assertInstanceOf(ImageFile::class, $image);
    }

    public function testExists_FileDoesNotExist_FalseIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $filenameKey = $this->givenFilenameKey(self::FILENAME_NOT_EXIST);

        $exists = $source->exists($filenameKey);

        $this->assertFalse($exists);
    }

    public function testExists_FileExists_TrueIsReturned(): void
    {
        $source = $this->createFilesystemSource();
        $this->givenFileOperations_IsFile_Returns($this->fileOperations, self::FILENAME_EXISTS_FULL, true);
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);

        $exists = $source->exists($filenameKey);

        $this->assertTrue($exists);
    }

    private function createFilesystemSource(string $baseDirectory = self::BASE_DIRECTORY): FilesystemSource
    {
        $this->givenFileOperations_IsDirectory_Returns($this->fileOperations, self::BASE_DIRECTORY, true);

        $source = new FilesystemSource($baseDirectory, $this->fileOperations, $this->imageFactory);

        return $source;
    }

    private function givenFilenameKey($filename): FilenameKeyInterface
    {
        $filenameKey = \Phake::mock(FilenameKeyInterface::class);

        \Phake::when($filenameKey)->getValue()->thenReturn($filename);

        return $filenameKey;
    }

    private function givenImageFactory_CreateImageFile_ReturnsImageFile($imageFilename): ImageFile
    {
        $imageFile = \Phake::mock(ImageFile::class);

        \Phake::when($this->imageFactory)
            ->createImageFile($imageFilename)
            ->thenReturn($imageFile);

        return $imageFile;
    }
}
