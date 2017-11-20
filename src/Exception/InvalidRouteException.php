<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Exception;

use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;

/**
 * Description of InvalidRouteException
 *
 * @author Igor Lazarev <lazarev@lightsoft.ru>
 */
class InvalidRouteException extends ApplicationException
{
    public function __construct(
        string $message = '',
        int $code = HttpStatusCodeEnum::NOT_FOUND,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
