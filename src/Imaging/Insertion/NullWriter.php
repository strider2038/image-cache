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
use Strider2038\ImgCache\Exception\NotAllowedException;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class NullWriter implements ImageWriterInterface
{
    public function exists(string $key): bool
    {
        throw new NotAllowedException('Method is not allowed');
    }

    public function insert(string $key, StreamInterface $data): void
    {
        throw new NotAllowedException('Method is not allowed');
    }

    public function delete(string $key): void
    {
        throw new NotAllowedException('Method is not allowed');
    }
}
