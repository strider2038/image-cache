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
use Strider2038\ImgCache\Imaging\Image\AbstractImage;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

class AbstractImageTest extends TestCase
{
    public function testConstruct_GivenSaveOptions_GetSaveOptionsReturns(): void
    {
        $saveOptions = $this->givenSaveOptions();
        $image = $this->createImage($saveOptions);

        $this->assertSame($saveOptions, $image->getSaveOptions());
    }

    public function testSetSaveOptions_GivenSaveOptions_GetSaveOptionsReturns(): void
    {
        $givenSaveOptions = $this->givenSaveOptions();
        $image = $this->createImage($givenSaveOptions);
        $setSaveOptions = $this->givenSaveOptions();

        $image->setSaveOptions($setSaveOptions);

        $this->assertSame($setSaveOptions, $image->getSaveOptions());
        $this->assertNotSame($givenSaveOptions, $image->getSaveOptions());
    }

    private function givenSaveOptions(): SaveOptions
    {
        $saveOptions = \Phake::mock(SaveOptions::class);

        return $saveOptions;
    }

    private function createImage($saveOptions)
    {
        $image = new class ($saveOptions) extends AbstractImage
        {
            public function saveTo(string $filename): void {}
            public function open(ProcessingEngineInterface $engine): ProcessingImageInterface {}
            public function render(): void {}
        };
        return $image;
    }
}
