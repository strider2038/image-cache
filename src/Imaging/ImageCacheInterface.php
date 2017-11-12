<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging;

use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFile;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageCacheInterface
{
    public function get(string $fileName): ImageFile;
    public function put(string $fileName, Image $image): void;
    public function deleteByMask(string $fileNameMask): void;
}
