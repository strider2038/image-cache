<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Imagick;

use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\Imagick\ImagickTransformer;
use Strider2038\ImgCache\Imaging\Processing\Imagick\ImagickTransformerFactory;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ImagickTransformerFactoryTest extends FileTestCase
{
    /** @var ImageFactoryInterface */
    private $imageFactory;

    protected function setUp()
    {
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
    }

    /** @test */
    public function createTransformerForImage_givenImage_ImagickTransformerIsReturned(): void
    {
        $factory = new ImagickTransformerFactory($this->imageFactory);
        $image = $this->givenImage();

        $transformer = $factory->createTransformerForImage($image);

        $this->assertInstanceOf(ImagickTransformer::class, $transformer);
    }

    private function givenImage(): Image
    {
        $image = \Phake::mock(Image::class);
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($image)->getData()->thenReturn($stream);
        $imageContents = file_get_contents($this->givenAssetFile(self::IMAGE_BOX_PNG));
        \Phake::when($stream)->getContents()->thenReturn($imageContents);

        return $image;
    }
}
