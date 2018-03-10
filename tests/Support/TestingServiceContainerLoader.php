<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support;

use Psr\Container\ContainerInterface;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationFactory;
use Strider2038\ImgCache\Configuration\ConfigurationLoader;
use Strider2038\ImgCache\Configuration\ConfigurationLoaderInterface;
use Strider2038\ImgCache\Configuration\ConfigurationTreeGenerator;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceFactory;
use Strider2038\ImgCache\Core\ApplicationParameters;
use Strider2038\ImgCache\Core\Service\FileContainerLoaderInterface;
use Strider2038\ImgCache\Core\Service\ServiceContainerLoaderInterface;
use Strider2038\ImgCache\Core\Service\YamlContainerLoader;
use Strider2038\ImgCache\Utility\YamlFileParser;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TestingServiceContainerLoader implements ServiceContainerLoaderInterface
{
    private const CONTAINER_FILENAME = 'config/testing.yml';
    private const CONFIGURATION_FILENAME_PREFIX = 'config/testing/';

    /** @var string */
    private $configurationFilename;

    /** @var FileLocatorInterface */
    private $fileLocator;
    /** @var FileContainerLoaderInterface */
    private $containerLoader;
    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;
    /** @var ContainerBuilder */
    private $container;

    public function __construct(string $configurationFilename)
    {
        $this->configurationFilename = self::CONFIGURATION_FILENAME_PREFIX . $configurationFilename;
    }

    public function loadServiceContainerWithApplicationParameters(ApplicationParameters $parameters): ContainerInterface
    {
        $this->createServices($parameters);
        $this->container = $this->containerLoader->loadContainerFromFile(self::CONTAINER_FILENAME);
        $configuration = $this->configurationLoader->loadConfigurationFromFile($this->configurationFilename);

        $containerParameters = $this->createContainerParameters($parameters, $configuration);
        $this->setParametersToContainer($containerParameters);

        return $this->container;
    }

    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }

    private function createServices(ApplicationParameters $parameters): void
    {
        $this->fileLocator = new FileLocator($parameters->getRootDirectory());
        $this->containerLoader = new YamlContainerLoader($this->fileLocator);
        $this->configurationLoader = new ConfigurationLoader(
            new YamlFileParser($this->fileLocator),
            new ConfigurationTreeGenerator(),
            new Processor(),
            new ConfigurationFactory(
                new ImageSourceFactory()
            )
        );
    }

    private function createContainerParameters(ApplicationParameters $parameters, Configuration $configuration): array
    {
        return [
            'application.directory' => $parameters->getRootDirectory(),
            'application.start_up_time' => $parameters->getStartUpTime(),
            'server_configuration' => $parameters->getServerConfiguration(),
            'access_control_token' => $configuration->getAccessControlToken(),
            'cached_image_quality' => $configuration->getCachedImageQuality(),
            'image_sources' => $configuration->getSourceCollection(),
        ];
    }

    private function setParametersToContainer(array $parameters): void
    {
        foreach ($parameters as $name => $value) {
            $this->container->setParameter($name, $value);
        }
    }
}
