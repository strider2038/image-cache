<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core\Streaming;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class NullStream implements StreamInterface
{
    public function __toString()
    {
        return '';
    }

    public function getContents(): string
    {
        return '';
    }

    public function close(): void
    {
    }

    public function getSize(): ? int
    {
        return null;
    }

    public function eof(): bool
    {
        return true;
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write(string $string): int
    {
        throw new \RuntimeException('Not implemented');
    }

    public function isReadable(): bool
    {
        return false;
    }

    public function read(int $length): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function rewind(): void
    {
    }
}
