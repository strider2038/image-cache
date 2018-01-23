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

use Strider2038\ImgCache\Configuration\ConfigurationLoaderInterface;
use Strider2038\ImgCache\Configuration\ConfigurationSetterInterface;
use Strider2038\ImgCache\Core\ErrorHandlerInterface;
use Strider2038\ImgCache\Core\ServiceLoaderInterface;
use Strider2038\ImgCache\Utility\RequestLoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ServiceLoader implements ServiceLoaderInterface
{
    /** @var ErrorHandlerInterface */
    private $errorHandler;

    /** @var RequestLoggerInterface */
    private $requestLogger;

    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;

    /** @var ConfigurationSetterInterface */
    private $configurationSetter;

    public function __construct(
        ErrorHandlerInterface $errorHandler,
        RequestLoggerInterface $requestLogger,
        ConfigurationLoaderInterface $configurationLoader,
        ConfigurationSetterInterface $configurationSetter
    ) {
        $this->errorHandler = $errorHandler;
        $this->requestLogger = $requestLogger;
        $this->configurationLoader = $configurationLoader;
        $this->configurationSetter = $configurationSetter;
    }

    public function loadServices(ContainerInterface $container): void
    {
        $this->errorHandler->register();
        $this->requestLogger->logClientRequest();
        $configuration = $this->configurationLoader->loadConfiguration();
        $this->configurationSetter->setConfigurationToContainer($configuration, $container);
    }
}
