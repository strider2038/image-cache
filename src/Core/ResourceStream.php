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
class ResourceStream implements StreamInterface
{
    public const MODE_READ_ONLY = 'r';
    public const MODE_READ_AND_WRITE = 'r+';
    public const MODE_WRITE_ONLY = 'w';
    public const MODE_WRITE_AND_READ = 'w+';
    public const MODE_APPEND_ONLY = 'a';
    public const MODE_APPEND_AND_READ = 'a+';
    public const MODE_WRITE_IF_NOT_EXIST = 'x';
    public const MODE_WRITE_AND_READ_IF_NOT_EXIST = 'x+';
    public const MODE_WRITE_WITHOUT_TRUNCATE = 'c';
    public const MODE_WRITE_AND_READ_WITHOUT_TRUNCATE = 'c+';

    /** @var resource */
    protected $resource;

    /** @var string */
    private $mode;

    public function __construct(string $stream, string $mode)
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
        return is_resource($this->resource) && $this->isModeWritable($this->mode);
    }

    public function write(string $string): int
    {
        return fwrite($this->resource, $string);
    }

    public function isReadable(): bool
    {
        return is_resource($this->resource) && $this->isModeReadable($this->mode);
    }

    public function read(int $length): string
    {
        return fread($this->resource, $length);
    }

    public function isModeReadable(string $mode): bool
    {
        return in_array(
            $mode,
            [
                self::MODE_READ_ONLY,
                self::MODE_READ_AND_WRITE,
                self::MODE_WRITE_AND_READ,
                self::MODE_APPEND_AND_READ,
                self::MODE_WRITE_AND_READ_IF_NOT_EXIST,
                self::MODE_WRITE_AND_READ_WITHOUT_TRUNCATE,
            ],
            true
        );
    }

    public function isModeWritable(string $mode): bool
    {
        return in_array(
            $mode,
            [
                self::MODE_READ_AND_WRITE,
                self::MODE_READ_AND_WRITE,
                self::MODE_WRITE_ONLY,
                self::MODE_WRITE_AND_READ,
                self::MODE_APPEND_ONLY,
                self::MODE_APPEND_AND_READ,
                self::MODE_WRITE_IF_NOT_EXIST,
                self::MODE_WRITE_AND_READ_IF_NOT_EXIST,
                self::MODE_WRITE_WITHOUT_TRUNCATE,
                self::MODE_WRITE_AND_READ_WITHOUT_TRUNCATE,
            ],
            true
        );
    }
}
