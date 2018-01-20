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

use Strider2038\ImgCache\Core\CoreServicesContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Application
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function run(): void
    {
        try {
            $this->loadServicesAndProcessRequest();
        } catch (\Throwable $exception) {
            header('HTTP/1.1 500 Internal server error');
            echo 'Application fatal error.';
        }
    }

    private function loadServicesAndProcessRequest(): void
    {
        /** @var CoreServicesContainerInterface $coreServices */
        $coreServices = $this->container->get(CoreServicesContainerInterface::class);

        $logger = $coreServices->getLogger();
        $logger->debug('Application started.');

        $serviceLoader = $coreServices->getServiceLoader();
        $request = $coreServices->getRequest();
        $requestHandler = $coreServices->getRequestHandler();
        $responseSender = $coreServices->getResponseSender();

        $serviceLoader->loadServices($this->container);
        $response = $requestHandler->handleRequest($request);
        $responseSender->send($response);

        $logger->debug(sprintf(
            'Application ended. Response %d %s was sent.',
            $response->getStatusCode()->getValue(),
            $response->getReasonPhrase()
        ));
    }
}
