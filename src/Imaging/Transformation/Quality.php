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
use Strider2038\ImgCache\Exception\{
    ApplicationException,
    InvalidImageException
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Quality implements TransformationInterface
{
    /** @var int */
    private $value;
    
    /** @var int */
    private $min = 15;
    
    /** @var int */
    private $max = 100;
    
    public function __construct(int $value)
    {
        if ($value < $this->min || $value > $this->max) {
            throw new InvalidImageException(
                "Wrong value for quality transformation. "
                . "Value must be between {$this->min} and {$this->max}."
            );
        }
        $this->value = $value;
    }
    
    public function getValue(): int
    {
        return $this->value;
    }
    
    public function apply(Image $image): void
    {
        throw new ApplicationException('This transformation cannot be applied to image');
    }
}
