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
class ExceptionResponse extends ErrorResponse {
    
    public function __construct(\Exception $ex, bool $isDebug = false) {
        $message = null;

        if ($isDebug) {
            $message = nl2br($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }
        
        parent::__construct(
            self::HTTP_CODE_INTERNAL_SERVER_ERROR,
            $message
        );
    }
    
}
