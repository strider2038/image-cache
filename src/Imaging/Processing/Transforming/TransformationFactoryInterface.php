<?php

namespace Strider2038\ImgCache\Imaging\Processing\Transforming;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface TransformationFactoryInterface
{
    public function createTransformation(string $stringParameters): TransformationInterface;
}
