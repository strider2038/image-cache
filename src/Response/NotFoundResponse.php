<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Response;

/**
 * Description of NotFoundResponse
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class NotFoundResponse extends ErrorResponse
{
    public function __construct() {
        parent::__construct(self::HTTP_CODE_NOT_FOUND);
    }
}
