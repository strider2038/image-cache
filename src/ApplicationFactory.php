<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache;

use Strider2038\ImgCache\Configuration\ConfigurationFactory;
use Strider2038\ImgCache\Configuration\ConfigurationLoader;
use Strider2038\ImgCache\Configuration\ConfigurationTreeGenerator;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceFactory;
use Strider2038\ImgCache\Core\Application;
use Strider2038\ImgCache\Core\ApplicationParameters;
use Strider2038\ImgCache\Core\ErrorHandler;
use Strider2038\ImgCache\Core\Service\SequentialServiceRunner;
use Strider2038\ImgCache\Core\Service\YamlContainerLoader;
use Strider2038\ImgCache\Service\ServiceContainerLoader;
use Strider2038\ImgCache\Utility\YamlFileParser;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationFactory
{
    private const DEFAULT_CONTAINER_FILENAME = 'config/main.yml';
    private const DEFAULT_CONFIGURATION_FILENAME = 'config/parameters.yml';

    public static function createApplication(ApplicationParameters $parameters): Application
    {
        $errorHandler = new ErrorHandler();
        $fileLocator = new FileLocator($parameters->getRootDirectory());
        $serviceContainerLoader = self::createServiceContainerLoader($fileLocator);
        $serviceRunner = new SequentialServiceRunner();

        return new Application(
            $parameters,
            $errorHandler,
            $serviceContainerLoader,
            $serviceRunner
        );
    }

    private static function createServiceContainerLoader(FileLocator $fileLocator): ServiceContainerLoader
    {
        $containerFilename = getenv('APP_CONTAINER_FILENAME') ?: self::DEFAULT_CONTAINER_FILENAME;
        $configurationFilename = getenv('APP_CONFIGURATION_FILENAME') ?: self::DEFAULT_CONFIGURATION_FILENAME;

        return new ServiceContainerLoader(
            new YamlContainerLoader($fileLocator),
            new ConfigurationLoader(
                new YamlFileParser($fileLocator),
                new ConfigurationTreeGenerator(),
                new Processor(),
                new ConfigurationFactory(
                    new ImageSourceFactory()
                )
            ),
            $containerFilename,
            $configurationFilename
        );
    }
}
