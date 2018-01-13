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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RotationParameters implements EntityInterface
{
    /**
     * @Assert\LessThanOrEqual(360)
     * @Assert\GreaterThanOrEqual(-360)
     * @var float
     */
    private $degree;

    public function __construct(float $degree)
    {
        $this->degree = $degree;
    }

    public function getId(): string
    {
        return 'rotation parameters';
    }

    public function getDegree(): float
    {
        return $this->degree;
    }
}
