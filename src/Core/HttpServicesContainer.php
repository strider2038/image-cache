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
class HttpServicesContainer implements HttpServicesContainerInterface
{
    /** @var RequestInterface */
    private $request;
    /** @var RequestHandlerInterface */
    private $requestHandler;
    /** @var ResponseSenderInterface */
    private $responseSender;

    public function __construct(
        RequestInterface $request,
        RequestHandlerInterface $requestHandler,
        ResponseSenderInterface $responseSender
    ) {
        $this->request = $request;
        $this->requestHandler = $requestHandler;
        $this->responseSender = $responseSender;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getRequestHandler(): RequestHandlerInterface
    {
        return $this->requestHandler;
    }

    public function getResponseSender(): ResponseSenderInterface
    {
        return $this->responseSender;
    }
}
