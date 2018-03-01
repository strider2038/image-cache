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

use Strider2038\ImgCache\Core\Service\ServiceContainerFactoryInterface;
use Strider2038\ImgCache\Core\Service\ServiceRunnerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Application
{
    /** @var ApplicationParameters */
    private $parameters;

    /** @var ErrorHandlerInterface */
    private $errorHandler;

    /** @var ServiceContainerFactoryInterface */
    private $serviceContainerFactory;

    /** @var ServiceRunnerInterface */
    private $serviceRunner;

    public function __construct(
        ApplicationParameters $parameters,
        ErrorHandlerInterface $errorHandler,
        ServiceContainerFactoryInterface $serviceContainerFactory,
        ServiceRunnerInterface $serviceRunner
    ) {
        $this->parameters = $parameters;
        $this->errorHandler = $errorHandler;
        $this->serviceContainerFactory = $serviceContainerFactory;
        $this->serviceRunner = $serviceRunner;
    }

    public function run(): void
    {
        $this->errorHandler->register();
        $serviceContainer = $this->serviceContainerFactory->createServiceContainerByApplicationParameters($this->parameters);
        $this->serviceRunner->runServices($serviceContainer);
    }
}
