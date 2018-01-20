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

use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class CoreServicesContainer implements CoreServicesContainerInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ServiceLoaderInterface */
    private $serviceLoader;

    /** @var RequestInterface */
    private $request;

    /** @var RequestHandlerInterface */
    private $requestHandler;

    /** @var ResponseSenderInterface */
    private $responseSender;

    public function __construct(
        LoggerInterface $logger,
        ServiceLoaderInterface $serviceLoader,
        RequestInterface $request,
        RequestHandlerInterface $requestHandler,
        ResponseSenderInterface $responseSender
    ) {
        $this->logger = $logger;
        $this->serviceLoader = $serviceLoader;
        $this->request = $request;
        $this->requestHandler = $requestHandler;
        $this->responseSender = $responseSender;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getServiceLoader(): ServiceLoaderInterface
    {
        return $this->serviceLoader;
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
