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

use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Source\FilesystemSource;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemSourceTest extends FileTestCase
{
    const FILENAME_NOT_EXIST = 'not.exist';
    const FILENAME_EXISTS_FULL = self::TEST_CACHE_DIR . '/cat.jpg';
    const FILENAME_EXISTS = 'cat.jpg';

    /** @var ImageFactoryInterface */
    private $imageFactory;
    
    public function setUp() 
    {
        parent::setUp();
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
    }
    
    public function testConstruct_BaseDirectoryExists_BaseDirectoryIsReturned(): void
    {
        $source = $this->createFilesystemSource();

        $this->assertEquals(self::TEST_CACHE_DIR . '/', $source->getBaseDirectory());
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Directory .* does not exist/
     */
    public function testConstruct_BaseDirectoryDoesNotExist_ExceptionThrown(): void
    {
        $this->createFilesystemSource(self::TEST_CACHE_DIR . '/not-exist');
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
        $this->givenAssetFile(self::IMAGE_BOX_PNG, self::FILENAME_EXISTS_FULL);
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);
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
        $this->givenAssetFile(self::IMAGE_BOX_PNG, self::FILENAME_EXISTS_FULL);
        $filenameKey = $this->givenFilenameKey(self::FILENAME_EXISTS);

        $exists = $source->exists($filenameKey);

        $this->assertTrue($exists);
    }

    private function createFilesystemSource(string $baseDirectory = self::TEST_CACHE_DIR): FilesystemSource
    {
        $source = new FilesystemSource($baseDirectory, $this->imageFactory);

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
