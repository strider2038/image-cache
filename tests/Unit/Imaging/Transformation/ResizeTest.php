<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging\Transformation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\Resize;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeTest extends TestCase
{
    /**
     * @dataProvider incorrectParamsProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidImageException
     * @expectedExceptionCode 400
     */
    public function testConstruct_IncorrectWidthHeigthOrMode_ExceptionThrown($width, $height, $mode): void
    {
        $this->createResize($width, $height, $mode);
    }

    public function incorrectParamsProvider(): array
    {
        return [
            [19, 100, Resize::MODE_FIT_IN],
            [2001, 100, Resize::MODE_FIT_IN],
            [100, 19, Resize::MODE_FIT_IN],
            [100, 2001, Resize::MODE_FIT_IN],
            [100, 100, 'unknown'],
        ];
    }

    public function testConstruct_ModeIsNotSet_DefaultModeReturned(): void
    {
        $resize = new Resize(100, 200);

        $this->assertEquals(100, $resize->getWidth());
        $this->assertEquals(200, $resize->getHeigth());
        $this->assertEquals(Resize::MODE_STRETCH, $resize->getMode());
    }

    public function testConstruct_HeightAndModeAreNotSet_DefaultsReturned(): void
    {
        $resize = new Resize(100);

        $this->assertEquals(100, $resize->getWidth());
        $this->assertEquals(100, $resize->getHeigth());
        $this->assertEquals(Resize::MODE_STRETCH, $resize->getMode());
    }

    /**
     * @dataProvider imagePropertiesProvider
     */
    public function testApply_TargetAndSourcePropsAreSet_ImageHasNewWidthAndHeight(
        int $sourceWidth,
        int $sourceHeight,
        int $resizeWidth,
        int $resizeHeight,
        string $resizeMode,
        int $finalWidth,
        int $finalHeight,
        int $cropX,
        int $cropY
    ): void {
        $image = $this->givenProcessingImage();
        $image->width = $sourceWidth;
        $image->height = $sourceHeight;

        $transformation = new Resize($resizeWidth, $resizeHeight, $resizeMode);
        $transformation->apply($image);

        $this->assertEquals($finalWidth, $image->width);
        $this->assertEquals($finalHeight, $image->height);
        $this->assertEquals($cropX, $image->cropX);
        $this->assertEquals($cropY, $image->cropY);
    }

    public function imagePropertiesProvider(): array
    {
        return [
            [200, 150, 300, 200, Resize::MODE_FIT_IN, 267, 200, 0, 0],
            [200, 150, 300, 200, Resize::MODE_STRETCH, 300, 200, 0, 13],
            [200, 150, 300, 200, Resize::MODE_PRESERVE_WIDTH, 300, 225, 0, 0],
            [200, 150, 300, 200, Resize::MODE_PRESERVE_HEIGHT, 267, 200, 0, 0],
            [200, 100, 60, 40, Resize::MODE_FIT_IN, 60, 30, 0, 0],
            [200, 100, 60, 40, Resize::MODE_STRETCH, 60, 40, 10, 0],
            [200, 100, 60, 40, Resize::MODE_PRESERVE_WIDTH, 60, 30, 0, 0],
            [200, 100, 60, 40, Resize::MODE_PRESERVE_HEIGHT, 80, 40, 0, 0],
            [200, 100, 40, 60, Resize::MODE_FIT_IN, 40, 20, 0, 0],
            [200, 100, 40, 60, Resize::MODE_STRETCH, 40, 60, 40, 0],
            [200, 100, 40, 60, Resize::MODE_PRESERVE_WIDTH, 40, 20, 0, 0],
            [200, 100, 40, 60, Resize::MODE_PRESERVE_HEIGHT, 120, 60, 0, 0],
        ];
    }

    private function createResize($width, $height, $mode): Resize
    {
        return new Resize($width, $height, $mode);
    }

    private function givenProcessingImage()
    {
        $image = new class implements ProcessingImageInterface
        {
            public $width;
            public $height;
            public $cropX = 0;
            public $cropY = 0;

            public function getWidth(): int
            {
                return $this->width;
            }

            public function getHeight(): int
            {
                return $this->height;
            }

            public function resize(int $width, int $heigth): void
            {
                $this->width = $width;
                $this->height = $heigth;
            }

            public function crop(int $width, int $heigth, int $x, int $y): void
            {
                $this->width = $width;
                $this->height = $heigth;
                $this->cropX = $x;
                $this->cropY = $y;
            }

            public function setSaveOptions(SaveOptions $saveOptions): void {}
            public function getSaveOptions(): SaveOptions {}
            public function saveTo(string $filename): void {}
            public function open(ProcessingEngineInterface $engine): ProcessingImageInterface {}
            public function render(): void {}
        };

        return $image;
    }
}
