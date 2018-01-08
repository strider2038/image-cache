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

use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeTransformationFactory implements TransformationFactoryInterface
{
    public function createTransformation(string $configuration): TransformationInterface
    {
        $isValid = preg_match(
            '/^(\d+)(x(\d+)){0,1}([fswh]{1}){0,1}$/', 
            strtolower($configuration),
            $matches
        );

        if (!$isValid) {
            throw new InvalidRequestValueException('Invalid config for resize transformation');
        }

        $width = $matches[1] ?? 0;
        $height = !empty($matches[3]) ? (int) $matches[3] : $width;
        $modeCode = $matches[4] ?? null;

        if (ResizeModeEnum::isValid($modeCode)) {
            $mode = new ResizeModeEnum($modeCode);
        } else {
            $mode = new ResizeModeEnum(ResizeModeEnum::STRETCH);
        }

        $parameters = new ResizeParameters($width, $height, $mode);

        return new ResizeTransformation($parameters);
    }
}
