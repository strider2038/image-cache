<?php

namespace Strider2038\ImgCache\Core;

/**
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface RequestInterface 
{
    public function getMethod(): string;
    public function getHeader(string $key): ?string;
}
