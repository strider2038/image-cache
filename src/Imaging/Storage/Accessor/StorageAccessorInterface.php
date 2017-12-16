<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Accessor;

use Strider2038\ImgCache\Imaging\Image\Image;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface StorageAccessorInterface
{
    public function getImage(string $key): Image;
    public function imageExists(string $key): bool;
    public function putImage(string $key, Image $image): void;
    public function deleteImage(string $key): void;
}
