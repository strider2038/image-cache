<?php

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
