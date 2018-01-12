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

use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RotatingTransformation implements TransformationInterface
{
    /** @var RotationParameters */
    private $parameters;

    public function __construct(RotationParameters $parameters)
    {
        $this->parameters = $parameters;
    }

    public function apply(ImageTransformerInterface $transformer): void
    {
        $transformer->rotate($this->parameters->getDegree());
    }
}
