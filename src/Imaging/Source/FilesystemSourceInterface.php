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

use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface FilesystemSourceInterface
{
    public function get(FilenameKeyInterface $key): ? ImageInterface;
    public function exists(FilenameKeyInterface $key): bool;
    public function put(FilenameKeyInterface $key, StreamInterface $stream): void;
    public function delete(FilenameKeyInterface $key): void;
}
