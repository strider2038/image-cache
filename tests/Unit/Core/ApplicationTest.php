<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Strider2038\ImgCache\Core\Application;
use Strider2038\ImgCache\Core\ApplicationParameters;
use Strider2038\ImgCache\Core\ErrorHandlerInterface;
use Strider2038\ImgCache\Core\Service\ServiceContainerLoaderInterface;
use Strider2038\ImgCache\Core\Service\ServiceRunnerInterface;

class ApplicationTest extends TestCase
{
    /** @var ApplicationParameters */
    private $parameters;

    /** @var ErrorHandlerInterface */
    private $errorHandler;

    /** @var ServiceContainerLoaderInterface */
    private $serviceContainerLoader;

    /** @var \Strider2038\ImgCache\Core\Service\ServiceRunnerInterface */
    private $serviceRunner;

    protected function setUp(): void
    {
        $this->parameters = \Phake::mock(ApplicationParameters::class);
        $this->errorHandler = \Phake::mock(ErrorHandlerInterface::class);
        $this->serviceContainerLoader = \Phake::mock(ServiceContainerLoaderInterface::class);
        $this->serviceRunner = \Phake::mock(ServiceRunnerInterface::class);
    }

    /** @test */
    public function run_givenParametersAndServices_errorHandlerRegisteredAndServiceContainerCreatedAndServicesInContainerRun(): void
    {
        $application = new Application(
            $this->errorHandler,
            $this->serviceContainerLoader,
            $this->serviceRunner
        );
        $serviceContainer = $this->givenServiceContainerLoader_loadServiceContainerWithApplicationParameters_returnsServiceContainer();

        $application->run($this->parameters);

        $this->assertErrorHandler_register_isCalledOnce();
        $this->assertServiceContainerFactory_loadServiceContainerWithApplicationParameters_isCalledOnceWithParameters();
        $this->assertServiceRunner_runServices_isCalledOnceWithServiceContainer($serviceContainer);
    }

    private function givenServiceContainerLoader_loadServiceContainerWithApplicationParameters_returnsServiceContainer(): ContainerInterface
    {
        $serviceContainer = \Phake::mock(ContainerInterface::class);
        \Phake::when($this->serviceContainerLoader)
            ->loadServiceContainerWithApplicationParameters(\Phake::anyParameters())
            ->thenReturn($serviceContainer);

        return $serviceContainer;
    }

    private function assertErrorHandler_register_isCalledOnce(): void
    {
        \Phake::verify($this->errorHandler, \Phake::times(1))->register();
    }

    private function assertServiceContainerFactory_loadServiceContainerWithApplicationParameters_isCalledOnceWithParameters(): void
    {
        \Phake::verify($this->serviceContainerLoader, \Phake::times(1))
            ->loadServiceContainerWithApplicationParameters($this->parameters);
    }

    private function assertServiceRunner_runServices_isCalledOnceWithServiceContainer(ContainerInterface $serviceContainer): void
    {
        \Phake::verify($this->serviceRunner, \Phake::times(1))
            ->runServices($serviceContainer);
    }
}
