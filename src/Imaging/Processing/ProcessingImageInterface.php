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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ProcessingImageInterface
{
    public function getWidth(): int;
    public function getHeight(): int;
    public function resize(int $width, int $heigth): void;
    public function crop(int $width, int $heigth, int $x, int $y): void;
}