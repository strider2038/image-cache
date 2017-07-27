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

use Strider2038\ImgCache\Imaging\Image\AbstractImage;

/**
 * @todo Add layer support http://php.net/manual/ru/imagick.coalesceimages.php
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickImage extends AbstractImage implements ProcessingImageInterface
{
    /** @var \Imagick */
    private $imagick;
    
    public function __construct(\Imagick $processor)
    {
        $this->imagick = $processor;
    }
    
    public function getHeight(): int
    {
        $this->imagick->getImageHeight();
    }

    public function getWidth(): int
    {
        $this->imagick->getImageWidth();
    }

    public function crop(int $width, int $height, int $x, int $y): void
    {
        $this->imagick->cropImage($width, $height, $x, $y);
    }

    public function resize(int $width, int $height): void
    {
        $this->imagick->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);
    }

    public function saveTo(string $filename): void
    {
        $this->imagick->writeImage($filename);
    }

    public function open(ProcessingEngineInterface $engine): ProcessingImageInterface
    {
        // TODO: Implement open() method.
    }

    public function render(): void
    {
        // TODO: Implement render() method.
    }
}
