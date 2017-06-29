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

use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Transformation\{
    TransformationBuilderInterface,
    TransformationInterface
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationsBuilderInterfaceMock implements TransformationBuilderInterface
{
    public function build(string $config): TransformationInterface
    {
        return new class implements TransformationInterface {
            public function apply(ProcessingImageInterface $image): void {}
        };
    }
}
