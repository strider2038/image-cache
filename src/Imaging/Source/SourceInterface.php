<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source;

use Strider2038\ImgCache\Imaging\Image;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface SourceInterface
{
    public function get(string $filename): Image;
    public function put(string $filename, Image $image): void;
}
