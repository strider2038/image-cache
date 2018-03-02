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
use Strider2038\ImgCache\Core\Service\FileContainerLoaderInterface;
use Strider2038\ImgCache\Service\ServiceContainerLoader;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

class ServiceContainerLoaderTest extends TestCase
{
    private const ROOT_DIRECTORY = 'root_directory';
    private const SERVER_CONFIGURATION = ['server_configuration'];
    private const CONTAINER_FILENAME = 'config/main.yml';
    private const CONFIGURATION_FILENAME = 'config/parameters.yml';
    private const ACCESS_CONTROL_TOKEN = 'access_control_token';
    private const CACHED_IMAGE_QUALITY = 85;

    /** @var FileContainerLoaderInterface */
    private $containerLoader;

    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;

    protected function setUp(): void
    {
        $this->containerLoader = \Phake::mock(FileContainerLoaderInterface::class);
        $this->configurationLoader = \Phake::mock(ConfigurationLoaderInterface::class);
    }

    /** @test */
    public function loadServiceContainerWithApplicationParameters_givenParameters_serviceContainerAndConfigurationLoadedAndContainerParametersSet(): void
    {
        $factory = $this->createServiceContainerFactory();
        $applicationParameters = $this->givenApplicationParameters();
        $createdContainer = $this->givenContainerLoader_loadContainerFromFile_returnsContainer();
        $configuration = $this->givenConfigurationLoader_loadConfigurationFromFile_returnsConfiguration();

        $container = $factory->loadServiceContainerWithApplicationParameters($applicationParameters);

        $this->assertInstanceOf(PsrContainerInterface::class, $container);
        $this->assertSame($createdContainer, $container);
        $this->assertContainerLoader_loadContainerFromFile_isCalledOnceWithFilename(self::CONTAINER_FILENAME);
        $this->assertConfigurationLoader_loadConfigurationFromFile_isCalledOnceWithFilename(self::CONFIGURATION_FILENAME);
        $this->assertContainerParametersSetter_setParametersToContainer_isCalledOnceWithContainerAndValidParameters($container);
    }

    private function createServiceContainerFactory(): ServiceContainerLoader
    {
        return new ServiceContainerLoader(
            $this->containerLoader,
            $this->configurationLoader
        );
    }

    private function givenApplicationParameters(): ApplicationParameters
    {
        return new ApplicationParameters(
            self::ROOT_DIRECTORY,
            self::SERVER_CONFIGURATION
        );
    }

    private function assertContainerLoader_loadContainerFromFile_isCalledOnceWithFilename(string $containerFilename): void
    {
        \Phake::verify($this->containerLoader, \Phake::times(1))
            ->loadContainerFromFile($containerFilename);
    }

    private function givenContainerLoader_loadContainerFromFile_returnsContainer(): SymfonyContainerInterface
    {
        $serviceContainer = \Phake::mock(SymfonyContainerInterface::class);
        \Phake::when($this->containerLoader)
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
        $this->assertContainer_setParameter_isCalledOnceWithNameAndValue($container, 'application.directory', self::ROOT_DIRECTORY);
        $startUpTime = $this->assertContainer_setParameter_isCalledOnceWithNameAndCapturedValue($container, 'application.start_up_time');
        $this->assertGreaterThan(0, $startUpTime);
        $this->assertContainer_setParameter_isCalledOnceWithNameAndValue($container, 'server_configuration', self::SERVER_CONFIGURATION);
        $this->assertContainer_setParameter_isCalledOnceWithNameAndValue($container, 'access_control_token', self::ACCESS_CONTROL_TOKEN);
        $this->assertContainer_setParameter_isCalledOnceWithNameAndValue($container, 'cached_image_quality', self::CACHED_IMAGE_QUALITY);
        $imageSources = $this->assertContainer_setParameter_isCalledOnceWithNameAndCapturedValue($container, 'image_sources');
        $this->assertInstanceOf(ImageSourceCollection::class, $imageSources);
    }

    private function assertContainer_setParameter_isCalledOnceWithNameAndValue(
        SymfonyContainerInterface $container,
        string $name,
        $value
    ): void {
        \Phake::verify($container, \Phake::times(1))
            ->setParameter($name, $value);
    }

    private function assertContainer_setParameter_isCalledOnceWithNameAndCapturedValue(
        SymfonyContainerInterface $container,
        string $name
    ) {
        \Phake::verify($container, \Phake::times(1))
            ->setParameter($name, \Phake::capture($value));

        return $value;
    }
}
