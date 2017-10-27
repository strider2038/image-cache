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

use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\Imagick\ImagickTransformer;
use Strider2038\ImgCache\Imaging\Processing\Rectangle;
use Strider2038\ImgCache\Imaging\Processing\Size;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ImagickTransformerTest extends FileTestCase
{
    private const IMAGE_SOURCE_HEIGHT = 4;
    private const IMAGE_SOURCE_WIDTH = 4;
    private const IMAGE_FINAL_WIDTH = 8;
    private const IMAGE_FINAL_HEIGHT = 8;

    /** @var \Imagick */
    private $imagick;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    protected function setUp()
    {
        $this->imagick = new \Imagick($this->givenAssetFile(self::IMAGE_BOX_JPG));
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
    }

    /** @test */
    public function resize_givenSize_imageIsResize(): void
    {
        $transformer = $this->createTransformer();
        $size = new Size(self::IMAGE_FINAL_WIDTH, self::IMAGE_FINAL_HEIGHT);

        $returnedTransformer = $transformer->resize($size);

        $this->assertSame($returnedTransformer, $transformer);
        $finalSize = $transformer->getSize();
        $this->assertEquals($size->getHeight(), $finalSize->getHeight());
        $this->assertEquals($size->getWidth(), $finalSize->getWidth());
    }

    /** @test */
    public function crop_givenSize_imageIsCropped(): void
    {
        $transformer = $this->createTransformer();
        $rectangle = new Rectangle(self::IMAGE_SOURCE_WIDTH - 2, self::IMAGE_SOURCE_HEIGHT - 1, 1, 1);

        $returnedTransformer = $transformer->crop($rectangle);

        $this->assertSame($returnedTransformer, $transformer);
        $size = $transformer->getSize();
        $this->assertEquals(2, $size->getWidth());
        $this->assertEquals(3, $size->getHeight());
    }

    /** @test */
    public function getImage_givenImage_imageIsReturnedWithTheSameContents(): void
    {
        $transformer = $this->createTransformer();
        $expectedData = $this->imagick->getImageBlob();
        $createdImage = $this->givenImageFactory_createFromData_returnsImage();

        $image = $transformer->getImage();

        $this->assertImageFactory_createFromData_isCalledOnceWith($expectedData);
        $this->assertSame($createdImage, $image);
    }

    private function createTransformer(): ImagickTransformer
    {
        return new ImagickTransformer($this->imagick, $this->imageFactory);
    }

    private function givenImageFactory_createFromData_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createFromData(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }

    private function assertImageFactory_createFromData_isCalledOnceWith($expectedData): void
    {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createFromData($expectedData);
    }
}
