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
class HttpMethodEnum extends Enum
{
    public const OPTIONS = 'OPTIONS';
    public const HEAD = 'HEAD';
    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const PATCH = 'PATCH';
    public const DELETE = 'DELETE';

    public function isReadMethod(): bool
    {
        return \in_array(
            $this->value,
            [
                self::OPTIONS,
                self::HEAD,
                self::GET,
            ],
            true
        );
    }
}
