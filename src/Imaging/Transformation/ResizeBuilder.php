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

use Strider2038\ImgCache\Exception\InvalidConfigException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeBuilder implements TransformationBuilderInterface
{
    public function build(string $config): TransformationInterface
    {
        $isValid = preg_match(
            '/^(\d+)(x(\d+)){0,1}([fswh]{1}){0,1}$/', 
            strtolower($config), 
            $matches
        );
        if (!$isValid) {
            throw new InvalidConfigException('Invalid config for resize transformation');
        }
        $width = $matches[1] ?? 0;
        $heigth = !empty($matches[3]) ? (int) $matches[3] : null;
        $mode = Resize::MODE_STRETCH;
        $modeCode = $matches[4] ?? null;
        switch ($modeCode) {
            case 'f': $mode = Resize::MODE_FIT_IN; break;
            case 's': $mode = Resize::MODE_STRETCH; break;
            case 'w': $mode = Resize::MODE_PRESERVE_WIDTH; break;
            case 'h': $mode = Resize::MODE_PRESERVE_HEIGHT; break;
        }
        return new Resize($width, $heigth, $mode);
    }
}
