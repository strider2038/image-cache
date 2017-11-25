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
use Strider2038\ImgCache\Exception\InvalidValueException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResourceStream implements StreamInterface
{
    /** @var resource */
    protected $resource;

    /** @var ResourceStreamModeEnum */
    private $mode;

    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidValueException('Invalid resource descriptor');
        }

        $meta = stream_get_meta_data($resource);

        if (ResourceStreamModeEnum::isValid($meta['mode'])) {
            $this->mode = new ResourceStreamModeEnum($meta['mode']);
        } else {
            throw new InvalidValueException('Unsupported resource mode');
        }

        $this->resource = $resource;
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
        if ($this->mode->isWritable()) {
            return fwrite($this->resource, $string);
        }

        throw new \RuntimeException('Resource is read only');
    }

    public function isReadable(): bool
    {
        return is_resource($this->resource) && $this->mode->isReadable();
    }

    public function read(int $length): string
    {
        if ($this->mode->isReadable()) {
            return fread($this->resource, $length);
        }

        throw new \RuntimeException('Resource is write only');
    }
}
