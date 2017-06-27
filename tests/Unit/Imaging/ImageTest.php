<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Strider2038\ImgCache\Tests\Support\{
    TestImages,
    FileTestCase
};
use Strider2038\ImgCache\Imaging\Image;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageTest extends FileTestCase
{
    /**
     * @expectedException Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     */
    public function testConstruct_FileDoesNotExist_ExceptionThrown(): void
    {
        new Image(self::TEST_DIR . '/not.existing');
    }

    public function testConstruct_FileExists_FileNameIsCorrect(): void
    {
        $filename = TestImages::getFilename('cat300.jpg');
        
        $image = new Image($filename);
        
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals($filename, $image->getFilename());
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidImageException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage has unsupported mime type
     */
    public function testConstruct_FileHasInvalidMimeType_ExceptionThrown(): void
    {
        $filename = self::TEST_DIR . '/text.txt';
        file_put_contents($filename, 'test_data');
        
        new Image($filename);
    }
}