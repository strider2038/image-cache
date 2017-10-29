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

use Strider2038\ImgCache\Exception\InvalidValueException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Size implements SizeInterface
{
    /** @var int */
    private $width;

    /** @var int */
    private $height;

    public function __construct(int $width, int $height)
    {
        if ($width <= 0 || $height <= 0) {
            throw new InvalidValueException('Width or height cannot be less than or equal to 0');
        }
        $this->width = $width;
        $this->height = $height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
