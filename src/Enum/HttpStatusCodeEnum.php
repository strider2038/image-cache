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

use MyCLabs\Enum\Enum;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class HttpStatusCodeEnum extends Enum
{
    public const OK                     = 200;
    public const CREATED                = 201;
    public const ACCEPTED               = 202;
    public const RESET_CONTENT          = 205;
    public const MOVED_PERMANENTLY      = 301;
    public const FOUND                  = 302;
    public const NOT_MODIFIED           = 304;
    public const BAD_REQUEST            = 400;
    public const UNAUTHORIZED           = 401;
    public const FORBIDDEN              = 403;
    public const NOT_FOUND              = 404;
    public const METHOD_NOT_ALLOWED     = 405;
    public const CONFLICT               = 409;
    public const UNSUPPORTED_MEDIA_TYPE = 415;
    public const INTERNAL_SERVER_ERROR  = 500;
}