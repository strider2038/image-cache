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
 * @todo Add layer support http://php.net/manual/ru/imagick.coalesceimages.php
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickImage implements ProcessingImageInterface
{
    /** @var \Imagick */
    private $imagick;
    
    public function __construct(\Imagick $processor)
    {
        $this->imagick = $processor;
    }
    
    public function getHeight(): int
    {
        $this->imagick->getimageheight();
    }

    public function getWidth(): int
    {
        $this->imagick->getimagewidth();
    }

    public function crop(int $width, int $heigth, int $x, int $y): void
    {
        $this->imagick->cropimage($width, $heigth, $x, $y);
    }

    public function resize(int $width, int $heigth): void
    {
        $this->imagick->resizeimage($width, $heigth, \Imagick::FILTER_LANCZOS, 1);
    }

}
