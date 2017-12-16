<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Enum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class WebDAVMethodEnum extends HttpMethodEnum
{
    public const PROPFIND = 'PROPFIND';
    public const PROPPATCH = 'PROPPATCH';
    public const MKCOL = 'MKCOL';
    public const COPY = 'COPY';
    public const MOVE = 'MOVE';
    public const LOCK = 'LOCK';
    public const UNLOCK = 'UNLOCK';
}
