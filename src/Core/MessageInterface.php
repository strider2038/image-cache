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
interface MessageInterface
{
    public function getProtocolVersion(): string;
    public function getHeaders(): HeaderCollection;
    public function hasHeader(string $name): bool;
    public function getHeader(string $name): HeaderValueCollection;
    public function getHeaderLine(string $name): string;
    public function getBody(): StreamInterface;
}