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

    /** @var string */
    private $message = '';

    public function __construct(\Exception $exception, bool $isDebug = false) {
        if ($isDebug) {
            $this->message = sprintf(
                "Application exception #%d '%s' in file: %s (%d)\n\nStack trace:\n%s\n",
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            );
        }

        parent::__construct($exception->getCode(), nl2br($this->message));
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
