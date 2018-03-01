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
use Strider2038\ImgCache\Core\ServiceContainerFactoryInterface;
use Strider2038\ImgCache\Core\ServiceRunnerInterface;

class ApplicationTest extends TestCase
{
    /** @var ApplicationParameters */
    private $parameters;

    /** @var ErrorHandlerInterface */
    private $errorHandler;

    /** @var ServiceContainerFactoryInterface */
    private $serviceContainerFactory;

    /** @var ServiceRunnerInterface */
    private $serviceRunner;

    protected function setUp(): void
    {
        $this->parameters = \Phake::mock(ApplicationParameters::class);
        $this->errorHandler = \Phake::mock(ErrorHandlerInterface::class);
        $this->serviceContainerFactory = \Phake::mock(ServiceContainerFactoryInterface::class);
        $this->serviceRunner = \Phake::mock(ServiceRunnerInterface::class);
    }

    /** @test */
    public function run_givenParametersAndServices_errorHandlerRegisteredAndServiceContainerCreatedAndServicesInContainerRun(): void
    {
        $application = new Application(
            $this->parameters,
            $this->errorHandler,
            $this->serviceContainerFactory,
            $this->serviceRunner
        );
        $serviceContainer = $this->givenServiceContainerFactory_createServiceContainerByApplicationParameters_returnsServiceContainer();

        $application->run();

        $this->assertErrorHandler_register_isCalledOnce();
        $this->assertServiceContainerFactory_createServiceContainerByApplicationParameters_isCalledOnceWithParameters();
        $this->assertServiceRunner_runServices_isCalledOnceWithServiceContainer($serviceContainer);
    }

    private function givenServiceContainerFactory_createServiceContainerByApplicationParameters_returnsServiceContainer(): ContainerInterface
    {
        $serviceContainer = \Phake::mock(ContainerInterface::class);
        \Phake::when($this->serviceContainerFactory)
            ->createServiceContainerByApplicationParameters(\Phake::anyParameters())
            ->thenReturn($serviceContainer);

        return $serviceContainer;
    }

    private function assertErrorHandler_register_isCalledOnce(): void
    {
        \Phake::verify($this->errorHandler, \Phake::times(1))->register();
    }

    private function assertServiceContainerFactory_createServiceContainerByApplicationParameters_isCalledOnceWithParameters(): void
    {
        \Phake::verify($this->serviceContainerFactory, \Phake::times(1))
            ->createServiceContainerByApplicationParameters($this->parameters);
    }

    private function assertServiceRunner_runServices_isCalledOnceWithServiceContainer(ContainerInterface $serviceContainer): void
    {
        \Phake::verify($this->serviceRunner, \Phake::times(1))
            ->runServices($serviceContainer);
    }
}
