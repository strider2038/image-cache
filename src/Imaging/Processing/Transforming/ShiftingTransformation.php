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

use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Processing\PointInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ShiftingTransformation implements TransformationInterface
{
    /** @var PointInterface */
    private $parameters;

    public function __construct(PointInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): PointInterface
    {
        return $this->parameters;
    }

    public function apply(ImageTransformerInterface $transformer): void
    {
        $transformer->shift($this->parameters);
    }
}
