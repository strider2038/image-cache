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

use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Processing\Size;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeParameters extends Size
{
    private const MIN_VALUE = 20;
    private const MAX_VALUE = 2000;

    /** @var ResizeModeEnum */
    private $mode;

    public function __construct(int $width, int $height, ResizeModeEnum $mode)
    {
        parent::__construct($width, $height);
        $this->mode = $mode;
        $this->validateValue('Width', $width);
        $this->validateValue('Height', $height);
    }

    public function getMode(): ResizeModeEnum
    {
        return $this->mode;
    }

    private function validateValue(string $name, int $value): void
    {
        if ($value < self::MIN_VALUE || $value > self::MAX_VALUE) {
            $message = sprintf(
                '%s of the image must be between %d and %d',
                $name,
                self::MIN_VALUE,
                self::MAX_VALUE
            );
            throw new InvalidRequestValueException($message);
        }
    }
}
