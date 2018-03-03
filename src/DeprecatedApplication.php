<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache;

use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\CoreServicesContainerInterface;
use Strider2038\ImgCache\Core\HttpServicesContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 * @deprecated
 */
class DeprecatedApplication
{
    /** @var ContainerInterface */
    private $container;
    /** @var LoggerInterface */
    private $logger;
    /** @var Callable */
    private $fatalHandler;

    public function __construct(ContainerInterface $container, Callable $fatalHandler = null)
    {
        $this->container = $container;
        $this->fatalHandler = $fatalHandler ?? function (\Throwable $exception) {
            header('HTTP/1.1 500 Internal server error');
            echo 'Application fatal error: ' . $exception;
        };
    }
    
    public function run(): void
    {
        try {
            $this->loadServices();
            $this->processRequest();
        } catch (\Throwable $exception) {
            \call_user_func($this->fatalHandler, $exception);
        }
    }

    private function loadServices(): void
    {
        /** @var CoreServicesContainerInterface $coreServices */
        $coreServices = $this->container->get(CoreServicesContainerInterface::class);

        $this->logger = $coreServices->getLogger();
        $this->logger->debug('Application started.');

        $serviceLoader = $coreServices->getServiceLoader();
        $serviceLoader->loadServices($this->container);
    }

    private function processRequest(): void
    {
        /** @var HttpServicesContainerInterface $httpServices */
        $httpServices = $this->container->get(HttpServicesContainerInterface::class);

        $request = $httpServices->getRequest();
        $requestHandler = $httpServices->getRequestHandler();
        $responseSender = $httpServices->getResponseSender();

        $response = $requestHandler->handleRequest($request);
        $responseSender->sendResponse($response);

        $this->logger->debug(sprintf(
            'Application ended. Response %d %s was sent.',
            $response->getStatusCode()->getValue(),
            $response->getReasonPhrase()
        ));
    }
}
