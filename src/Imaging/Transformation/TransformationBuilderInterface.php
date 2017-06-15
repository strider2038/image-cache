<?php

namespace Strider2038\ImgCache\Imaging\Transformation;

use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;

/**
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface TransformationBuilderInterface
{
    public function build(string $config): TransformationInterface;
}
