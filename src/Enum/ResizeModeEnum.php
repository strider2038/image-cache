<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Enum;

use MyCLabs\Enum\Enum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeModeEnum extends Enum
{
    public const FIT_IN = 'f';
    public const STRETCH = 's';
    public const PRESERVE_WIDTH = 'w';
    public const PRESERVE_HEIGHT = 'h';
}
