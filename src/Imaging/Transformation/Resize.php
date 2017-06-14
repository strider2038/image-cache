<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Transformation;

use Strider2038\ImgCache\Imaging\Image;
use Strider2038\ImgCache\Exception\InvalidImageException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Resize implements TransformationInterface
{
    const MODE_FIT_IN = 'fitIn';
    const MODE_STRETCH = 'stretch';
    const MODE_PRESERVE_WIDTH = 'preserveWidth';
    const MODE_PRESERVE_HEIGHT = 'preserveHeight';
    
    /** @var int */
    private $minWidth = 20;
    
    /** @var int */
    private $maxWidth = 1500;
    
    /** @var int */
    private $minHeigth = 20;
    
    /** @var int */
    private $maxHeigth = 1500;
    
    /** @var int */
    private $width;
    
    /** @var int */
    private $heigth;
    
    /** @var string */
    private $mode;
    
    public function __construct(int $width, ?int $heigth = null, string $mode = self::MODE_STRETCH)
    {
        if ($heigth === null) {
            $heigth = $width;
        }
        if ($width < $this->minWidth || $width > $this->maxWidth) {
            throw new InvalidImageException(
                "Width of the image must be between {$this->minWidth} and {$this->maxWidth}"
            );
        }
        if ($heigth < $this->minHeigth || $heigth > $this->maxHeigth) {
            throw new InvalidImageException(
                "Height of the image must be between {$this->minHeigth} and {$this->maxHeigth}"
            );
        }
        if (!in_array($mode, self::getAvailableModes())) {
            throw new InvalidImageException("Undefined resize mode: '{$mode}'");
        }
        $this->width = $width;
        $this->heigth = $heigth;
        $this->mode = $mode;
    }
    
    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeigth(): int
    {
        return $this->heigth;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function apply(Image $image): void
    {
        
    }
    
    public static function getAvailableModes(): array
    {
        return [
            self::MODE_FIT_IN,
            self::MODE_STRETCH,
            self::MODE_PRESERVE_WIDTH,
            self::MODE_PRESERVE_HEIGHT,
        ];
    }
}
