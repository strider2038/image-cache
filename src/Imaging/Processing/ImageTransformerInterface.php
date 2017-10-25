<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing;

use Strider2038\ImgCache\Imaging\Image\Image;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageTransformerInterface
{
    public function resize(int $width, int $height): ImageTransformerInterface;
    public function crop(int $width, int $height, int $x, int $y): ImageTransformerInterface;
    public function getImage(): Image;
}
