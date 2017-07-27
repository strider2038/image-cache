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
use Strider2038\ImgCache\Imaging\Image\ImageInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageCacheInterface
{
    public function get(string $key): ?ImageInterface;
    public function put(string $key, $data): void;
    public function delete(string $key): void;
    public function rebuild(string $key): void;
    public function exists(string $key): bool;
}
