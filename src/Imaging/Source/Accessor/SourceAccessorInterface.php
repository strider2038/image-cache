<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source\Accessor;

use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface SourceAccessorInterface
{
    public function get(string $key): ? Image;
    public function exists(string $key): bool;
    public function put(string $key, StreamInterface $stream): void;
    public function delete(string $key): void;
}
