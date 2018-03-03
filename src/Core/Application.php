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

use Strider2038\ImgCache\Core\Service\ServiceContainerLoaderInterface;
use Strider2038\ImgCache\Core\Service\ServiceRunnerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Application
{
    /** @var ErrorHandlerInterface */
    private $errorHandler;

    /** @var ServiceContainerLoaderInterface */
    private $serviceContainerLoader;

    /** @var ServiceRunnerInterface */
    private $serviceRunner;

    public function __construct(
        ErrorHandlerInterface $errorHandler,
        ServiceContainerLoaderInterface $serviceContainerLoader,
        ServiceRunnerInterface $serviceRunner
    ) {
        $this->errorHandler = $errorHandler;
        $this->serviceContainerLoader = $serviceContainerLoader;
        $this->serviceRunner = $serviceRunner;
    }

    public function run(ApplicationParameters $parameters): void
    {
        $this->errorHandler->register();
        $serviceContainer = $this->serviceContainerLoader->loadServiceContainerWithApplicationParameters($parameters);
        $this->serviceRunner->runServices($serviceContainer);
    }
}
