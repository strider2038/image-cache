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

use Strider2038\ImgCache\Exception\InvalidImageException;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Resize implements TransformationInterface
{
    const MODE_FIT_IN = 'fitIn';
    const MODE_STRETCH = 'stretch';
    const MODE_PRESERVE_WIDTH = 'preserveWidth';
    const MODE_PRESERVE_HEIGHT = 'preserveHeight';
    
    const MIN_WIDTH = 20;
    const MAX_WIDTH = 2000;
    const MIN_HEIGHT = 20;
    const MAX_HEIGHT = 2000;
    
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

        if ($width < static::MIN_WIDTH || $width > static::MAX_WIDTH) {
            throw new InvalidImageException(
                "Width of the image must be between " . static::MIN_WIDTH 
                    . " and " . static::MAX_HEIGHT
            );
        }

        if ($heigth < static::MIN_HEIGHT || $heigth > static::MAX_HEIGHT) {
            throw new InvalidImageException(
                "Height of the image must be between " . static::MIN_HEIGHT 
                    . " and " . static::MAX_HEIGHT
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

    public function apply(ProcessingImageInterface $image): void
    {
        $sourceWidth = $image->getWidth();
        $sourceHeight = $image->getHeight();
        
        $ratios = [
            (float) $this->width / (float) $sourceWidth,
            (float) $this->heigth / (float) $sourceHeight,
        ];
        
        $ratio = 1;
        switch ($this->mode) {
            case self::MODE_FIT_IN: $ratio = min($ratios); break;
            case self::MODE_STRETCH: $ratio = max($ratios); break;
            case self::MODE_PRESERVE_WIDTH: $ratio = $ratios[0]; break;
            case self::MODE_PRESERVE_HEIGHT: $ratio = $ratios[1]; break;
        }
        
        $newWidth = round($sourceWidth * $ratio);
        $newHeight = round($sourceHeight * $ratio);
        $image->resize($newWidth, $newHeight);
        
        if ($this->mode === self::MODE_STRETCH) {
            $image->crop(
                $this->width,
                $this->heigth, 
                max(0, round(($newWidth - $this->width) / 2)), 
                max(0, round(($newHeight - $this->heigth) / 2))
            );
        }
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
