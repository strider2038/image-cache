<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Strider2038\ImgCache\Imaging\Image;
use Strider2038\ImgCache\Imaging\Source\FileSource;
use Strider2038\ImgCache\Core\TemporaryFilesManagerInterface;
use Strider2038\ImgCache\Tests\Support\{
    TestImages,
    FileTestCase
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileSourceTest extends FileTestCase
{
    const IMAGE_NAME = 'cat300.jpg';
    
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
        $source = new FileSource($this->manager, self::TEST_DIR);
        
        $this->assertDirectoryExists(self::TEST_DIR);
        $this->assertDirectoryIsReadable(self::TEST_DIR);
        $this->assertDirectoryIsWritable(self::TEST_DIR);
        $this->assertEquals(self::TEST_DIR, $source->getBaseDirectory());
        
        $source2 = new FileSource($this->manager, self::TEST_DIR . '/');
        $this->assertEquals(
            self::TEST_DIR, 
            $source2->getBaseDirectory(),
            'Base directory name should be without trailing slash'
        );
    }
    
    /**
     * @expectedException Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     */
    public function testGet_FileDoesNotExist_FileNotFoundExceptionThrown(): void
    {
        $source = new FileSource($this->manager, self::TEST_DIR);
        $source->get('not.exist');
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
        $manager->testTempFilename = self::TEST_DIR . '/test.jpg';
        $source = new FileSource($manager, self::TEST_DIR);
        mkdir(self::TEST_DIR . '/somedir');
        copy(TestImages::getFilename(self::IMAGE_NAME), $sourceFilename);
        
        $image = $source->get($fileKey);
        
        $this->assertInstanceOf(Image::class, $image);
        $this->assertFileExists($manager->testTempFilename);
    }

    public function imageFilenameProvider(): array
    {
        return [
            [self::TEST_DIR . '/a.jpg', 'a.jpg'],
            [self::TEST_DIR . '/somedir/b.jpg', 'somedir/b.jpg'],
            [self::TEST_DIR . '/somedir/c.jpg', '/somedir/c.jpg'],
        ];
    }
}
