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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class CoreServicesContainer implements CoreServicesContainerInterface
{
    /** @var LoggerInterface */
    private $logger;
    /** @var ServiceLoaderInterface */
    private $serviceLoader;

    public function __construct(
        LoggerInterface $logger,
        ServiceLoaderInterface $serviceLoader
    ) {
        $this->logger = $logger;
        $this->serviceLoader = $serviceLoader;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getServiceLoader(): ServiceLoaderInterface
    {
        return $this->serviceLoader;
    }
}
