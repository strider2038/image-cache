<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Image;

use Strider2038\ImgCache\Core\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageParameters implements EntityInterface
{
    public const QUALITY_VALUE_DEFAULT = 85;

    /** @var int */
    private $quality = self::QUALITY_VALUE_DEFAULT;

    public function getId(): string
    {
        return 'image parameters';
    }

    /**
     * @Assert\GreaterThanOrEqual(15)
     * @Assert\LessThanOrEqual(100)
     * @return int
     */
    public function getQuality(): int
    {
        return $this->quality;
    }

    public function setQuality(int $quality): void
    {
        $this->quality = $quality;
    }
}
