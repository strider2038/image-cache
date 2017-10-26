<?php

namespace Strider2038\ImgCache\Imaging\Transformation;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface TransformationFactoryInterface
{
    public function create(string $configuration): TransformationInterface;
}
