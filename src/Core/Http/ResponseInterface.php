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

use Strider2038\ImgCache\Enum\HttpStatusCode;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ResponseInterface extends MessageInterface
{
    public function getStatusCode(): HttpStatusCode;
    public function getReasonPhrase(): string;
}