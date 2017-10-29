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

use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResourceStream implements StreamInterface
{
    /** @var resource */
    protected $resource;

    /** @var ResourceStreamModeEnum */
    private $mode;

    public function __construct(string $stream, ResourceStreamModeEnum $mode)
    {
        $this->resource = fopen($stream, $mode);
        $this->mode = $mode;
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
        return is_resource($this->resource) && $this->mode->isWritable();
    }

    public function write(string $string): int
    {
        return fwrite($this->resource, $string);
    }

    public function isReadable(): bool
    {
        return is_resource($this->resource) && $this->mode->isReadable();
    }

    public function read(int $length): string
    {
        return fread($this->resource, $length);
    }
}
