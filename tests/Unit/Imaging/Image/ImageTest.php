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
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

class ImageTest extends TestCase
{
    /** @var SaveOptions */
    private $saveOptions;

    /** @var StreamInterface */
    private $data;

    protected function setUp()
    {
        $this->saveOptions = \Phake::mock(SaveOptions::class);
        $this->data = \Phake::mock(StreamInterface::class);
    }

    /** @test */
    public function construct_givenSaveOptionsAndData_SaveOptionsAndDataAreAccessible(): void
    {
        $image = new Image($this->saveOptions, $this->data);

        $this->assertSame($this->saveOptions, $image->getSaveOptions());
        $this->assertSame($this->data, $image->getData());
    }
}
