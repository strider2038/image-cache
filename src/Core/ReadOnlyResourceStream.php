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
class ReadOnlyResourceStream implements StreamInterface
{
    /** @var resource */
    private $resource;

    public function __construct(string $stream)
    {
        $this->resource = fopen($stream, 'r');
    }

    public function __destruct()
    {
        if (is_resource($this->resource)) {
            $this->close();
        }
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function getContents(): string
    {
        return stream_get_contents($this->resource);
    }

    public function close(): void
    {
        fclose($this->resource);
    }

    public function getSize(): ? int
    {
        $data = fstat($this->resource);

        return $data['size'] ?? null;
    }

    public function eof(): bool
    {
        return feof($this->resource);
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
        return is_resource($this->resource);
    }

    public function read(int $length): string
    {
        return fread($this->resource, $length);
    }
}
