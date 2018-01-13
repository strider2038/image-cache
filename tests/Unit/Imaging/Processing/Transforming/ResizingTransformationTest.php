<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Transforming;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Processing\RectangleInterface;
use Strider2038\ImgCache\Imaging\Processing\Size;
use Strider2038\ImgCache\Imaging\Processing\SizeInterface;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ResizeParameters;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ResizingTransformation;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizingTransformationTest extends TestCase
{
    /**
     * @test
     * @dataProvider imagePropertiesProvider
     * @param int $sourceWidth
     * @param int $sourceHeight
     * @param int $resizeWidth
     * @param int $resizeHeight
     * @param string $resizeMode
     * @param int $finalWidth
     * @param int $finalHeight
     * @param int $cropX
     * @param int $cropY
     */
    public function apply_targetAndSourcePropertiesAreSet_imageHasNewWidthAndHeight(
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
        $transformer = $this->givenImageTransformer();
        $transformer->width = $sourceWidth;
        $transformer->height = $sourceHeight;
        $parameters = new ResizeParameters($resizeWidth, $resizeHeight, new ResizeModeEnum($resizeMode));
        $transformation = new ResizingTransformation($parameters);

        $transformation->apply($transformer);

        $this->assertEquals($finalWidth, $transformer->width);
        $this->assertEquals($finalHeight, $transformer->height);
        $this->assertEquals($cropX, $transformer->cropX);
        $this->assertEquals($cropY, $transformer->cropY);
    }

    public function imagePropertiesProvider(): array
    {
        return [
            [200, 150, 300, 200, ResizeModeEnum::FIT_IN, 267, 200, 0, 0],
            [200, 150, 300, 200, ResizeModeEnum::STRETCH, 300, 200, 0, 13],
            [200, 150, 300, 200, ResizeModeEnum::PRESERVE_WIDTH, 300, 225, 0, 0],
            [200, 150, 300, 200, ResizeModeEnum::PRESERVE_HEIGHT, 267, 200, 0, 0],
            [200, 100, 60, 40, ResizeModeEnum::FIT_IN, 60, 30, 0, 0],
            [200, 100, 60, 40, ResizeModeEnum::STRETCH, 60, 40, 10, 0],
            [200, 100, 60, 40, ResizeModeEnum::PRESERVE_WIDTH, 60, 30, 0, 0],
            [200, 100, 60, 40, ResizeModeEnum::PRESERVE_HEIGHT, 80, 40, 0, 0],
            [200, 100, 40, 60, ResizeModeEnum::FIT_IN, 40, 20, 0, 0],
            [200, 100, 40, 60, ResizeModeEnum::STRETCH, 40, 60, 40, 0],
            [200, 100, 40, 60, ResizeModeEnum::PRESERVE_WIDTH, 40, 20, 0, 0],
            [200, 100, 40, 60, ResizeModeEnum::PRESERVE_HEIGHT, 120, 60, 0, 0],
        ];
    }

    private function givenImageTransformer()
    {
        $image = new class implements ImageTransformerInterface
        {
            public $width;
            public $height;
            public $cropX = 0;
            public $cropY = 0;

            public function resize(SizeInterface $size): ImageTransformerInterface
            {
                $this->width = $size->getWidth();
                $this->height = $size->getHeight();
                return $this;
            }

            public function crop(RectangleInterface $rectangle): ImageTransformerInterface
            {
                $this->width = $rectangle->getWidth();
                $this->height = $rectangle->getHeight();
                $this->cropX = $rectangle->getLeft();
                $this->cropY = $rectangle->getTop();
                return $this;
            }

            public function getSize(): SizeInterface
            {
                return new Size($this->width, $this->height);
            }

            public function flip(): ImageTransformerInterface {}
            public function flop(): ImageTransformerInterface {}
            public function rotate(float $degree): ImageTransformerInterface {}
            public function getImage(): Image {}
            public function setCompressionQuality(int $quality): ImageTransformerInterface {}
            public function writeToFile(string $filename): ImageTransformerInterface {}
            public function getData(): StreamInterface {}
        };

        return $image;
    }
}
