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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface UriInterface
{
    public function getScheme(): string;
    public function getAuthority(): string;
    public function getUserInfo(): string;
    public function getHost(): string;
    public function getPort(): ? int;
    public function getPath(): string;
    public function getQuery(): string;
    public function getFragment(): string;
    public function __toString();
}
