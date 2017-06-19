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

use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Exception\{
    ApplicationException,
    InvalidImageException
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Quality implements TransformationInterface
{
    const MIN_VALUE = 15;
    const MAX_VALUE = 100;
    
    /** @var int */
    private $value;
    
    public function __construct(int $value)
    {
        if ($value < static::MIN_VALUE || $value > static::MAX_VALUE) {
            throw new InvalidImageException(
                "Wrong value for quality transformation. "
                . "Value must be between " . static::MIN_VALUE . " and " 
                . static::MAX_VALUE . "."
            );
        }
        $this->value = $value;
    }
    
    public function getValue(): int
    {
        return $this->value;
    }
    
    public function apply(ProcessingImageInterface $image): void
    {
        throw new ApplicationException('This transformation cannot be applied to image');
    }
}
