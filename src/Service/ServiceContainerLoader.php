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
use Strider2038\ImgCache\Core\Service\ContainerParametersSetterInterface;
use Strider2038\ImgCache\Core\Service\ServiceContainerLoaderInterface;
use Strider2038\ImgCache\Core\Service\ServiceLoaderInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ServiceContainerLoader implements ServiceContainerLoaderInterface
{
    private const CONTAINER_FILENAME = 'config/main.yml';
    private const CONFIGURATION_FILENAME = 'config/parameters.yml';

    /** @var ServiceLoaderInterface */
    private $serviceLoader;

    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;

    /** @var ContainerParametersSetterInterface */
    private $containerParametersSetter;

    public function __construct(
        ServiceLoaderInterface $serviceLoader,
        ConfigurationLoaderInterface $configurationLoader,
        ContainerParametersSetterInterface $containerParametersSetter
    ) {
        $this->serviceLoader = $serviceLoader;
        $this->configurationLoader = $configurationLoader;
        $this->containerParametersSetter = $containerParametersSetter;
    }

    public function loadServiceContainerWithApplicationParameters(ApplicationParameters $parameters): ContainerInterface
    {
        $container = $this->serviceLoader->loadContainerFromFile(self::CONTAINER_FILENAME);
        $configuration = $this->configurationLoader->loadConfigurationFromFile(self::CONFIGURATION_FILENAME);

        $containerParameters = $this->createContainerParameters($parameters, $configuration);
        $this->containerParametersSetter->setParametersToContainer($container, $containerParameters);

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
}
