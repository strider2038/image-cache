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
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationLoaderInterface;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceCollection;
use Strider2038\ImgCache\Core\ApplicationParameters;
use Strider2038\ImgCache\Core\Service\ContainerParametersSetterInterface;
use Strider2038\ImgCache\Core\Service\ServiceLoaderInterface;
use Strider2038\ImgCache\Service\ServiceContainerLoader;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

class ServiceContainerFactoryTest extends TestCase
{
    private const ROOT_DIRECTORY = 'root_directory';
    private const SERVER_CONFIGURATION = ['server_configuration'];
    private const CONTAINER_FILENAME = 'config/main.yml';
    private const CONFIGURATION_FILENAME = 'config/parameters.yml';
    private const ACCESS_CONTROL_TOKEN = 'access_control_token';
    private const CACHED_IMAGE_QUALITY = 85;

    /** @var ServiceLoaderInterface */
    private $serviceLoader;

    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;

    /** @var ContainerParametersSetterInterface */
    private $containerParametersSetter;

    protected function setUp(): void
    {
        $this->serviceLoader = \Phake::mock(ServiceLoaderInterface::class);
        $this->configurationLoader = \Phake::mock(ConfigurationLoaderInterface::class);
        $this->containerParametersSetter = \Phake::mock(ContainerParametersSetterInterface::class);
    }

    /** @test */
    public function createServiceContainerByApplicationParameters_givenParameters_serviceContainerAndConfigurationLoadedAndContainerParametersSet(): void
    {
        $factory = $this->createServiceContainerFactory();
        $applicationParameters = $this->givenApplicationParameters();
        $createdContainer = $this->givenServiceLoader_loadContainerFromFile_returnsContainer();
        $configuration = $this->givenConfigurationLoader_loadConfigurationFromFile_returnsConfiguration();

        $container = $factory->loadServiceContainerWithApplicationParameters($applicationParameters);

        $this->assertInstanceOf(PsrContainerInterface::class, $container);
        $this->assertSame($createdContainer, $container);
        $this->assertServiceLoader_loadContainerFromFile_isCalledOnceWithFilename(self::CONTAINER_FILENAME);
        $this->assertConfigurationLoader_loadConfigurationFromFile_isCalledOnceWithFilename(self::CONFIGURATION_FILENAME);
        $this->assertContainerParametersSetter_setParametersToContainer_isCalledOnceWithContainerAndValidParameters($container);
    }

    private function createServiceContainerFactory(): ServiceContainerLoader
    {
        return new ServiceContainerLoader(
            $this->serviceLoader,
            $this->configurationLoader,
            $this->containerParametersSetter
        );
    }

    private function givenApplicationParameters(): ApplicationParameters
    {
        return new ApplicationParameters(
            self::ROOT_DIRECTORY,
            self::SERVER_CONFIGURATION
        );
    }

    private function assertServiceLoader_loadContainerFromFile_isCalledOnceWithFilename(string $containerFilename): void
    {
        \Phake::verify($this->serviceLoader, \Phake::times(1))
            ->loadContainerFromFile($containerFilename);
    }

    private function givenServiceLoader_loadContainerFromFile_returnsContainer(): SymfonyContainerInterface
    {
        $serviceContainer = \Phake::mock(SymfonyContainerInterface::class);
        \Phake::when($this->serviceLoader)
            ->loadContainerFromFile(\Phake::anyParameters())
            ->thenReturn($serviceContainer);

        return $serviceContainer;
    }

    private function assertConfigurationLoader_loadConfigurationFromFile_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->configurationLoader, \Phake::times(1))
            ->loadConfigurationFromFile($filename);
    }

    private function givenConfigurationLoader_loadConfigurationFromFile_returnsConfiguration(): Configuration
    {
        $configuration = new Configuration(
            self::ACCESS_CONTROL_TOKEN,
            self::CACHED_IMAGE_QUALITY,
            new ImageSourceCollection()
        );

        \Phake::when($this->configurationLoader)
            ->loadConfigurationFromFile(\Phake::anyParameters())
            ->thenReturn($configuration);

        return $configuration;
    }

    private function assertContainerParametersSetter_setParametersToContainer_isCalledOnceWithContainerAndValidParameters(
        SymfonyContainerInterface $container
    ): void {
        \Phake::verify($this->containerParametersSetter, \Phake::times(1))
            ->setParametersToContainer($container, \Phake::capture($parameters));
        $this->assertArraySubset(['application.directory' => self::ROOT_DIRECTORY], $parameters);
        $this->assertArrayHasKey('application.start_up_time', $parameters);
        $this->assertArraySubset(['server_configuration' => self::SERVER_CONFIGURATION], $parameters);
        $this->assertArraySubset(['access_control_token' => self::ACCESS_CONTROL_TOKEN], $parameters);
        $this->assertArraySubset(['cached_image_quality' => self::CACHED_IMAGE_QUALITY], $parameters);
        $this->assertArrayHasKey('image_sources', $parameters);
        $this->assertInstanceOf(ImageSourceCollection::class, $parameters['image_sources']);
    }
}
