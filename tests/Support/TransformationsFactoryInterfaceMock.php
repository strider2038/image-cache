<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support;

use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationsFactoryInterfaceMock implements TransformationFactoryInterface
{
    public function createTransformation(string $stringParameters): TransformationInterface
    {
        return new class implements TransformationInterface {
            public function apply(ImageTransformerInterface $image): void {}
        };
    }
}
