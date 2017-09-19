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

use Strider2038\ImgCache\Core\DeprecatedResponse;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileOperationException extends ApplicationException
{
    public function __construct(
        $message = "",
        \Throwable $previous = null
    ) {
        parent::__construct($message, DeprecatedResponse::HTTP_CODE_INTERNAL_SERVER_ERROR, $previous);
    }
}