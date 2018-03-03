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
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ConfigurationLoader implements ConfigurationLoaderInterface
{
    /** @var ConfigurationFileParserInterface */
    private $fileParser;

    /** @var ConfigurationInterface */
    private $treeGenerator;

    /** @var Processor */
    private $processor;

    /** @var ConfigurationFactoryInterface */
    private $configurationFactory;

    public function __construct(
        ConfigurationFileParserInterface $fileParser,
        ConfigurationInterface $treeGenerator,
        Processor $processor,
        ConfigurationFactoryInterface $configurationFactory
    ) {
        $this->fileParser = $fileParser;
        $this->treeGenerator = $treeGenerator;
        $this->processor = $processor;
        $this->configurationFactory = $configurationFactory;
    }

    public function loadConfigurationFromFile(string $filename): Configuration
    {
        $configurationArray = $this->fileParser->parseConfigurationFile($filename);
        $processedConfiguration = $this->processConfiguration($configurationArray);

        return $this->configurationFactory->createConfiguration($processedConfiguration);
    }

    private function processConfiguration(array $configurationArray): array
    {
        return $this->processor->processConfiguration(
            $this->treeGenerator,
            [
                $configurationArray
            ]
        );
    }
}
