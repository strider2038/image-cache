<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging;

use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Tests\Support\{
    FileTestCase, TestImages
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageTest extends FileTestCase
{
    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     */
    public function testConstruct_FileDoesNotExist_ExceptionThrown(): void
    {
        new ImageFile(self::TEST_CACHE_DIR . '/not.existing');
    }

    public function testConstruct_FileExists_FileNameIsCorrect(): void
    {
        $filename = $this->givenFile(self::IMAGE_CAT300);
        
        $image = new ImageFile($filename);
        
        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertEquals($filename, $image->getFilename());
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidImageException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage has unsupported mime type
     */
    public function testConstruct_FileHasInvalidMimeType_ExceptionThrown(): void
    {
        $filename = self::TEST_CACHE_DIR . '/text.txt';
        file_put_contents($filename, 'test_data');
        
        new ImageFile($filename);
    }
}
