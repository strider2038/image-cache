<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image;
use Strider2038\ImgCache\Imaging\Source\FileSource;
use Strider2038\ImgCache\Core\TemporaryFilesManagerInterface;
use Strider2038\ImgCache\Tests\Support\TestImages;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileSourceTest extends TestCase
{
    const DIR_NAME = '/tmp/imgcache-test';
    const IMAGE_NAME = 'cat300.jpg';
    
    /** @var TemporaryFilesManagerInterface */
    private $manager;
    
    public function setUp() 
    {
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
                
        exec('rm -rf ' . self::DIR_NAME);
    }
    
    public function testConstruct_BaseDirectoryIsSet_BaseDirectoryCreated(): void
    {
        $this->assertDirectoryNotExists(self::DIR_NAME);
        
        $source = new FileSource($this->manager, self::DIR_NAME);
        
        $this->assertDirectoryExists(self::DIR_NAME);
        $this->assertDirectoryIsReadable(self::DIR_NAME);
        $this->assertDirectoryIsWritable(self::DIR_NAME);
        $this->assertEquals(self::DIR_NAME, $source->getBaseDirectory());
        
        $source2 = new FileSource($this->manager, self::DIR_NAME . '/');
        $this->assertEquals(
            self::DIR_NAME, 
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
        $source = new FileSource($this->manager, self::DIR_NAME);
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
        $manager->testTempFilename = self::DIR_NAME . '/test.jpg';
        $source = new FileSource($manager, self::DIR_NAME);
        mkdir(self::DIR_NAME . '/somedir');
        copy(TestImages::getFilename(self::IMAGE_NAME), $sourceFilename);
        
        $image = $source->get($fileKey);
        
        $this->assertInstanceOf(Image::class, $image);
        $this->assertFileExists($manager->testTempFilename);
    }

    public function imageFilenameProvider(): array
    {
        return [
            [self::DIR_NAME . '/a.jpg', 'a.jpg'],
            [self::DIR_NAME . '/somedir/b.jpg', 'somedir/b.jpg'],
            [self::DIR_NAME . '/somedir/c.jpg', '/somedir/c.jpg'],
        ];
    }
}
