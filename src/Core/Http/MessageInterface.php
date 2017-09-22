<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core\Http;

use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;
use Strider2038\ImgCache\Enum\HttpProtocolVersionEnum;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface MessageInterface
{
    public function getProtocolVersion(): HttpProtocolVersionEnum;
    public function getHeaders(): HeaderCollection;
    public function hasHeader(HttpHeaderEnum $name): bool;
    public function getHeader(HttpHeaderEnum $name): HeaderValueCollection;
    public function getHeaderLine(HttpHeaderEnum $name): string;
    public function getBody(): StreamInterface;
}