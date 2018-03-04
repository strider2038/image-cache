<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface HttpServicesContainerInterface
{
    public function getRequest(): RequestInterface;
    public function getRequestHandler(): RequestHandlerInterface;
    public function getResponseSender(): ResponseSenderInterface;
}