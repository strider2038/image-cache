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
class SaveOptions
{
    const QUALITY_VALUE_MIN = 15;
    const QUALITY_VALUE_MAX = 100;
    const QUALITY_VALUE_DEFAULT = 85;

    /** @var int */
    private $quality = self::QUALITY_VALUE_DEFAULT;

    public function getQuality(): int
    {
        return $this->quality;
    }

    /**
     * @param int|null $quality
     * @throws InvalidValueException
     */
    public function setQuality(int $quality): void
    {
        if ($quality < self::QUALITY_VALUE_MIN || $quality > self::QUALITY_VALUE_MAX) {
            throw new InvalidValueException(sprintf(
                "Quality value must be between %d and %d",
                self::QUALITY_VALUE_MIN,
                self::QUALITY_VALUE_MAX
            ));
        }
        $this->quality = $quality;
    }
}