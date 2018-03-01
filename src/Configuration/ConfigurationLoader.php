<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration;

use Strider2038\ImgCache\Utility\ConfigurationFileParserInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ConfigurationLoader implements ConfigurationLoaderInterface
{


    /** @var ConfigurationFileParserInterface */
    private $configurationFileParser;

    /** @var Processor */
    private $configurationProcessor;

    /** @var ConfigurationFactoryInterface */
    private $configurationFactory;

    public function __construct(
        ConfigurationFileParserInterface $configurationFileParser,
        Processor $configurationProcessor,
        ConfigurationFactoryInterface $configurationFactory
    ) {
        $this->configurationFileParser = $configurationFileParser;
        $this->configurationProcessor = $configurationProcessor;
        $this->configurationFactory = $configurationFactory;
    }

    public function loadConfigurationFromFile(string $filename): Configuration
    {
        $configurationArray = $this->configurationFileParser->parseConfigurationFile($filename);

        $applicationConfiguration = new ApplicationConfiguration();
        $processedConfiguration = $this->configurationProcessor->processConfiguration(
            $applicationConfiguration,
            [
                $configurationArray
            ]
        );

        return $this->configurationFactory->createConfiguration($processedConfiguration);
    }
}
