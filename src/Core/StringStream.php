<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class StringStream implements StreamInterface
{
    /** @var string */
    private $contents;

    /** @var int */
    private $position = 0;

    public function __construct(string $contents)
    {
        $this->contents = $contents;
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function close(): void
    {
    }

    public function getSize(): ? int
    {
        return strlen($this->contents);
    }

    public function eof(): bool
    {
        return $this->position >= strlen($this->contents);
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
        return true;
    }

    public function read(int $length): string
    {
        $length = max($length, 0);
        $string = substr($this->contents, $this->position, $length);
        $this->position += $length;

        return $string;
    }
}
