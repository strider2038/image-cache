<?php

namespace Strider2038\ImgCache\Response;

/**
 * Description of ExceptionResponse
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ExceptionResponse extends ErrorResponse {
    public function __construct(\Exception $ex) {
        parent::__construct(self::HTTP_CODE_INTERNAL_SERVER_ERROR);
        
    }
}
