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
        fclose($this->resource);
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function getContents(): string
    {
        return stream_get_contents($this->resource);
    }
}