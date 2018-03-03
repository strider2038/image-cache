<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core\Service;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ClientRequestProcessor implements ApplicationServiceInterface
{
    /** @var RequestInterface */
    private $request;

    /** @var RequestHandlerInterface */
    private $requestHandler;

    /** @var ResponseSenderInterface */
    private $responseSender;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        RequestInterface $request,
        RequestHandlerInterface $requestHandler,
        ResponseSenderInterface $responseSender
    ) {
        $this->request = $request;
        $this->requestHandler = $requestHandler;
        $this->responseSender = $responseSender;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function run(): void
    {
        $this->logger->debug('Starting to handle client request.');

        $response = $this->requestHandler->handleRequest($this->request);
        $this->responseSender->sendResponse($response);

        $this->logger->debug(
            sprintf(
                'Handling client request has been completed. Response %d %s was sent.',
                $response->getStatusCode()->getValue(),
                $response->getReasonPhrase()
            )
        );
    }
}
