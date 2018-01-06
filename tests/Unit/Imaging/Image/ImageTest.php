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
    private $parameters;

    /** @var StreamInterface */
    private $data;

    protected function setUp(): void
    {
        $this->parameters = \Phake::mock(ImageParameters::class);
        $this->data = \Phake::mock(StreamInterface::class);
    }

    /** @test */
    public function construct_givenImageParametersAndData_ImageParametersAndDataAreAccessible(): void
    {
        $image = new Image($this->data, $this->parameters);

        $this->assertSame($this->parameters, $image->getParameters());
        $this->assertSame($this->data, $image->getData());
    }

    /** @test */
    public function setParameters_givenParameters_ParametersAreSet(): void
    {
        $parameters = \Phake::mock(ImageParameters::class);
        $image = new Image($this->data, $this->parameters);

        $image->setParameters($parameters);

        $this->assertSame($parameters, $image->getParameters());
    }
}
