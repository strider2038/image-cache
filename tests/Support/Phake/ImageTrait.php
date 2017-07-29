<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support\Phake;

use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
trait ImageTrait
{
    protected function givenImage(): ImageInterface
    {
        $image = \Phake::mock(ImageInterface::class);

        return $image;
    }

    protected function givenProcessingImage(): ProcessingImageInterface
    {
        $image = \Phake::mock(ProcessingImageInterface::class);

        return $image;
    }
}