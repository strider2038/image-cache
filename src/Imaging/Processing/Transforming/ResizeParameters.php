<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing\Transforming;

use Strider2038\ImgCache\Core\EntityInterface;
use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeParameters implements EntityInterface
{
    /**
     * @Assert\GreaterThanOrEqual(20)
     * @Assert\LessThanOrEqual(2000)
     * @var int
     */
    private $width;

    /**
     * @Assert\GreaterThanOrEqual(20)
     * @Assert\LessThanOrEqual(2000)
     * @var int
     */
    private $height;

    /** @var ResizeModeEnum */
    private $mode;

    public function __construct(int $width, int $height, ResizeModeEnum $mode)
    {
        $this->width = $width;
        $this->height = $height;
        $this->mode = $mode;
    }

    public function getId(): string
    {
        return 'resize parameters';
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getMode(): ResizeModeEnum
    {
        return $this->mode;
    }
}
