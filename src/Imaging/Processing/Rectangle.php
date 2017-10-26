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
class Rectangle extends Size implements RectangleInterface
{
    /** @var int */
    private $left;

    /** @var int */
    private $top;

    public function __construct(int $width, int $height, int $left, int $top)
    {
        parent::__construct($width, $height);
        $this->left = $left;
        $this->top = $top;
    }

    public function getLeft(): int
    {
        return $this->left;
    }

    public function getTop(): int
    {
        return $this->top;
    }
}
