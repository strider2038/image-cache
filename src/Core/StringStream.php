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
}
