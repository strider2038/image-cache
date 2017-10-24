<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Image;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\ImageFile;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageFileTest extends TestCase
{
    private const FILENAME = '/tmp/file.jpg';

    /** @test */
    public function construct_givenFilename_fileNameIsSet(): void
    {
        $image = new ImageFile(self::FILENAME);

        $this->assertEquals(self::FILENAME, $image->getFilename());
    }
}
