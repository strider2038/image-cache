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
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SuccessResponse extends MessageResponse
{
    public function __construct($message = null)
    {
        parent::__construct(self::HTTP_CODE_OK, $message);
    }
}