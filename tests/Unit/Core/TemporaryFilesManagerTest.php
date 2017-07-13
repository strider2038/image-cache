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
use Strider2038\ImgCache\Core\TemporaryFilesManager;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TemporaryFilesManagerTest extends TestCase
{

    const TEMP_DIR = '/tmp/imgcache-test/';
    
    public function setUp() 
    {
        exec('rm -rf ' . self::TEMP_DIR . '*');
    }
    
    public function testConstruct_NoParams_NoErrors(): void
    {
        $manager = new TemporaryFilesManager(self::TEMP_DIR);
        $this->assertInstanceOf(TemporaryFilesManager::class, $manager);
    }
    
    public function testConstruct_CustomDirectory_CustomDirectoryCreated(): void
    {
        new TemporaryFilesManager(self::TEMP_DIR . 'custom/dir');
        $this->assertDirectoryExists(self::TEMP_DIR . 'custom/dir');
    }
    
    /**
     * @dataProvider fileKeysProvider
     */
    public function testGetHashedName_fileKeyIsSet_fileHashIsReturned(string $key, string $hash): void
    {
        $manager = new TemporaryFilesManager(self::TEMP_DIR);
        $this->assertEquals($hash, $manager->getHashedName($key));
    }

    public function fileKeysProvider(): array
    {
        return [
            ['x.jpg', self::TEMP_DIR . md5('x.jpg') . '.jpg'],
            ['/big/picture.png', self::TEMP_DIR . md5('/big/picture.png') . '.png'],
            ['blala_$.x', self::TEMP_DIR . md5('blala_$.x') . '.x'],
            ['noext', self::TEMP_DIR . md5('noext')],
        ];
    }
    
    public function testGetFilename_fileDoesNotExist_NullIsReturned(): void
    {
        $manager = new TemporaryFilesManager(self::TEMP_DIR);
        $this->assertNull($manager->getFilename('notexisting.file'));
    }
    
    public function testGetFilename_fileExists_FilenameIsReturned(): void
    {
        $filename = 'some.file';
        $manager = new TemporaryFilesManager(self::TEMP_DIR);
        file_put_contents($manager->getHashedName($filename), 'test');
        $tempFilename = $manager->getFilename($filename);
        $this->assertStringEndsWith('.file', $tempFilename);
        $this->assertFileExists($tempFilename);
        $this->assertFileIsReadable($tempFilename);
        $this->assertFileIsWritable($tempFilename);
        $this->assertEquals('test', file_get_contents($tempFilename));
    }
    
    public function testPutFile_fileDoesNotExist_FileIsCreatedAndFilenameIsReturned()
    {
        $filename = 'some.file';
        $contents = 'testdata';
        $manager = new TemporaryFilesManager(self::TEMP_DIR);
        $hashedName = $manager->getHashedName($filename);
        $this->assertEquals($hashedName, $manager->putFile($filename, $contents));
        $this->assertEquals(file_get_contents($hashedName), $contents);
    }
    
    public function testPutFile_fileCreatedTwice_ContentsIsRewrited()
    {
        $filename = 'some.file';
        $contents1 = 'testdata-1';
        $contents2 = 'testdata-2';
        $manager = new TemporaryFilesManager(self::TEMP_DIR);
        $hashedName = $manager->getHashedName($filename);
        $this->assertEquals($hashedName, $manager->putFile($filename, $contents1));
        $this->assertEquals(file_get_contents($hashedName), $contents1);
        $this->assertEquals($hashedName, $manager->putFile($filename, $contents2));
        $this->assertEquals(file_get_contents($hashedName), $contents2);
    }
}
