<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service;

use Psr\Container\ContainerInterface;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationLoaderInterface;
use Strider2038\ImgCache\Core\ApplicationParameters;
use Strider2038\ImgCache\Core\Service\FileContainerLoaderInterface;
use Strider2038\ImgCache\Core\Service\ServiceContainerLoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ServiceContainerLoader implements ServiceContainerLoaderInterface
{
    private const CONTAINER_FILENAME = 'config/main.yml';
    private const CONFIGURATION_FILENAME = 'config/parameters.yml';

    /** @var FileContainerLoaderInterface */
    private $containerLoader;

    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;

    public function __construct(
        FileContainerLoaderInterface $containerLoader,
        ConfigurationLoaderInterface $configurationLoader
    ) {
        $this->containerLoader = $containerLoader;
        $this->configurationLoader = $configurationLoader;
    }

    public function loadServiceContainerWithApplicationParameters(ApplicationParameters $parameters): ContainerInterface
    {
        $container = $this->containerLoader->loadContainerFromFile(self::CONTAINER_FILENAME);
        $configuration = $this->configurationLoader->loadConfigurationFromFile(self::CONFIGURATION_FILENAME);

        $containerParameters = $this->createContainerParameters($parameters, $configuration);
        $this->setParametersToContainer($container, $containerParameters);

        return $container;
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

    private function setParametersToContainer(SymfonyContainerInterface $container, array $parameters): void
    {
        foreach ($parameters as $name => $value) {
            $container->setParameter($name, $value);
        }
    }
}
