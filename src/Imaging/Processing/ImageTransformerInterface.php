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

use Strider2038\ImgCache\Core\Streaming\StreamInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageTransformerInterface
{
    public function resize(SizeInterface $size): ImageTransformerInterface;
    public function crop(RectangleInterface $rectangle): ImageTransformerInterface;
    public function flip(): ImageTransformerInterface;
    public function flop(): ImageTransformerInterface;
    public function rotate(float $degree): ImageTransformerInterface;
    public function setCompressionQuality(int $quality): ImageTransformerInterface;
    public function writeToFile(string $filename): ImageTransformerInterface;
    public function getSize(): SizeInterface;
    public function getData(): StreamInterface;
}
