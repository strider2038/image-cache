<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationLoaderInterface;
use Strider2038\ImgCache\Configuration\ContainerConfiguratorInterface;
use Strider2038\ImgCache\Core\ErrorHandlerInterface;
use Strider2038\ImgCache\Service\ServiceLoader;
use Strider2038\ImgCache\Utility\RequestLoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceLoaderTest extends TestCase
{
    /** @var ErrorHandlerInterface */
    private $errorHandler;
    /** @var RequestLoggerInterface */
    private $requestLogger;
    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;
    /** @var ContainerConfiguratorInterface */
    private $containerConfigurator;

    protected function setUp(): void
    {
        $this->errorHandler = \Phake::mock(ErrorHandlerInterface::class);
        $this->requestLogger = \Phake::mock(RequestLoggerInterface::class);
        $this->configurationLoader = \Phake::mock(ConfigurationLoaderInterface::class);
        $this->containerConfigurator = \Phake::mock(ContainerConfiguratorInterface::class);
    }

    /** @test */
    public function loadServices_givenContainer_servicesInitializedAndConfigurationLoadedAndSetToContainer(): void
    {
        $serviceLoader = $this->createServiceLoader();
        $container = \Phake::mock(ContainerInterface::class);
        $configuration = $this->givenConfigurationLoader_loadConfiguration_returnsConfiguration();

        $serviceLoader->loadServices($container);

        $this->assertErrorHandler_register_isCalledOnce();
        $this->assertRequestLogger_logClientRequest_isCalledOnce();
        $this->assertConfigurationLoader_loadConfiguration_isCalledOnce();
        $this->assertContainerConfigurator_updateContainerByConfiguration_isCalledOnceWithConfigurationAndContainer($configuration, $container);
    }

    private function createServiceLoader(): ServiceLoader
    {
        return new ServiceLoader(
            $this->errorHandler,
            $this->requestLogger,
            $this->configurationLoader,
            $this->containerConfigurator
        );
    }

    private function givenConfigurationLoader_loadConfiguration_returnsConfiguration(): Configuration
    {
        $configuration = \Phake::mock(Configuration::class);
        \Phake::when($this->configurationLoader)->loadConfiguration()->thenReturn($configuration);
        return $configuration;
    }

    private function assertErrorHandler_register_isCalledOnce(): void
    {
        \Phake::verify($this->errorHandler, \Phake::times(1))->register();
    }

    private function assertRequestLogger_logClientRequest_isCalledOnce(): void
    {
        \Phake::verify($this->requestLogger, \Phake::times(1))->logClientRequest();
    }

    private function assertConfigurationLoader_loadConfiguration_isCalledOnce(): void
    {
        \Phake::verify($this->configurationLoader, \Phake::times(1))->loadConfiguration();
    }

    private function assertContainerConfigurator_updateContainerByConfiguration_isCalledOnceWithConfigurationAndContainer(
        Configuration $configuration,
        ContainerInterface $container
    ): void {
        \Phake::verify($this->containerConfigurator, \Phake::times(1))
            ->updateContainerByConfiguration($container, $configuration);
    }
}
