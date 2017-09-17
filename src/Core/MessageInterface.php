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

use Strider2038\ImgCache\Enum\HttpHeader;
use Strider2038\ImgCache\Enum\HttpProtocolVersion;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface MessageInterface
{
    public function getProtocolVersion(): HttpProtocolVersion;
    public function getHeaders(): HeaderCollection;
    public function hasHeader(HttpHeader $name): bool;
    public function getHeader(HttpHeader $name): HeaderValueCollection;
    public function getHeaderLine(HttpHeader $name): string;
    public function getBody(): StreamInterface;
}