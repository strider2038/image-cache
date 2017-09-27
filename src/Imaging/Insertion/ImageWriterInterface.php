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

use Strider2038\ImgCache\Core\StreamInterface;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageWriterInterface
{
    public function exists(string $key): bool;
    public function insert(string $key, StreamInterface $data): void;
    public function delete(string $key): void;
}
