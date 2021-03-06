<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core\Http;

use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ResponseFactoryInterface
{
    public function createMessageResponse(HttpStatusCodeEnum $code, string $message = ''): ResponseInterface;
    public function createExceptionResponse(\Throwable $exception): ResponseInterface;
    public function createFileResponse(HttpStatusCodeEnum $code, string $filename): ResponseInterface;
}
