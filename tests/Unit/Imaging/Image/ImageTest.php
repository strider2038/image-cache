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
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;

class ImageTest extends TestCase
{
    /** @var ImageParameters */
    private $saveOptions;

    /** @var StreamInterface */
    private $data;

    protected function setUp()
    {
        $this->saveOptions = \Phake::mock(ImageParameters::class);
        $this->data = \Phake::mock(StreamInterface::class);
    }

    /** @test */
    public function construct_givenSaveOptionsAndData_SaveOptionsAndDataAreAccessible(): void
    {
        $image = new Image($this->saveOptions, $this->data);

        $this->assertSame($this->saveOptions, $image->getParameters());
        $this->assertSame($this->data, $image->getData());
    }

    /** @test */
    public function setSaveOptions_givenSaveOptions_SaveOptionsIsSet(): void
    {
        $saveOptions = \Phake::mock(ImageParameters::class);
        $image = new Image($this->saveOptions, $this->data);

        $image->setParameters($saveOptions);

        $this->assertSame($saveOptions, $image->getParameters());
    }
}
