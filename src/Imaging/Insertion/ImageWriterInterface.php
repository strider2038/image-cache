<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Insertion;

use Strider2038\ImgCache\Imaging\Image\Image;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageWriterInterface
{
    public function imageExists(string $key): bool;
    public function insertImage(string $key, Image $image): void;
    public function deleteImage(string $key): void;
    public function getImageFileNameMask(string $key): string;
}
