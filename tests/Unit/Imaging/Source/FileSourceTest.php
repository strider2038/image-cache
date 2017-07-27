<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Strider2038\ImgCache\Core\TemporaryFilesManagerInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Source\FileSource;
use Strider2038\ImgCache\Tests\Support\{
    FileTestCase, TestImages
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileSourceTest extends FileTestCase
{
    /** @var TemporaryFilesManagerInterface */
    private $manager;
    
    public function setUp() 
    {
        parent::setUp();
        $this->manager = new class implements TemporaryFilesManagerInterface {
            public function getFilename(string $fileKey): ?string 
            {
                return null;
            }
            public function putFile(string $fileKey, $data): string
            {
                return '';
            }
        };
    }
    
    public function testConstruct_BaseDirectoryIsSet_BaseDirectoryCreated(): void
    {
        $source = new FileSource($this->manager, self::TEST_CACHE_DIR);
        
        $this->assertDirectoryExists(self::TEST_CACHE_DIR);
        $this->assertDirectoryIsReadable(self::TEST_CACHE_DIR);
        $this->assertDirectoryIsWritable(self::TEST_CACHE_DIR);
        $this->assertEquals(self::TEST_CACHE_DIR, $source->getBaseDirectory());
        
        $source2 = new FileSource($this->manager, self::TEST_CACHE_DIR . '/');
        $this->assertEquals(
            self::TEST_CACHE_DIR,
            $source2->getBaseDirectory(),
            'Base directory name should be without trailing slash'
        );
    }
    
    public function testGet_FileDoesNotExist_FileNotFoundExceptionThrown(): void
    {
        $source = new FileSource($this->manager, self::TEST_CACHE_DIR);
        $this->assertNull($source->get('not.exist'));
    }
    
    /**
     * @dataProvider imageFilenameProvider
     */
    public function testGet_FileExists_ImageReturned(string $sourceFilename, string $fileKey): void
    {
        $manager = new class implements TemporaryFilesManagerInterface {
            public $testTempFilename;
            public function getFilename(string $fileKey): ?string 
            {
                return null;
            }
            public function putFile(string $fileKey, $data): string
            {
                file_put_contents($this->testTempFilename, $data);
                return $this->testTempFilename;
            }
        };
        $manager->testTempFilename = self::TEST_CACHE_DIR . '/test.jpg';
        $source = new FileSource($manager, self::TEST_CACHE_DIR);
        mkdir(self::TEST_CACHE_DIR . '/somedir');
        copy($this->givenFile(self::IMAGE_CAT300), $sourceFilename);
        
        $image = $source->get($fileKey);
        
        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertFileExists($manager->testTempFilename);
    }

    public function imageFilenameProvider(): array
    {
        return [
            [self::TEST_CACHE_DIR . '/a.jpg', 'a.jpg'],
            [self::TEST_CACHE_DIR . '/somedir/b.jpg', 'somedir/b.jpg'],
            [self::TEST_CACHE_DIR . '/somedir/c.jpg', '/somedir/c.jpg'],
        ];
    }
}
