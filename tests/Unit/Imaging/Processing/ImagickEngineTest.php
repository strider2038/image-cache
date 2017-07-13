<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing;

use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Imaging\Processing\{
    ImagickEngine,
    ImagickImage,
    ProcessingImageInterface
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickEngineTest extends FileTestCase
{
    /**
     * @expectedException \Exception
     */
    public function testOpen_FileDoesNotExist_ExceptionThrown(): void
    {
        $engine = new ImagickEngine();
        $engine->open(self::TEST_CACHE_DIR . '/a.jpg');
    }

    public function testOpen_FileExist_ImagickImageIsReturned(): void
    {
        $engine = new ImagickEngine();
        $image = $engine->open($this->haveFile(self::IMAGE_CAT300));
        $this->assertInstanceOf(ProcessingImageInterface::class, $image);
        $this->assertInstanceOf(ImagickImage::class, $image);
    }
}
