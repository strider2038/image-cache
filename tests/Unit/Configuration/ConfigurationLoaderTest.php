<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationFactoryInterface;
use Strider2038\ImgCache\Configuration\ConfigurationLoader;
use Strider2038\ImgCache\Utility\ConfigurationFileParserInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationLoaderTest extends TestCase
{
    private const CONFIGURATION_FILENAME = 'config/parameters.yml';

    /** @var ConfigurationFileParserInterface */
    private $fileParser;

    /** @var ConfigurationInterface */
    private $treeGenerator;

    /** @var Processor */
    private $processor;

    /** @var ConfigurationFactoryInterface */
    private $configurationFactory;

    protected function setUp(): void
    {
        $this->fileParser = \Phake::mock(ConfigurationFileParserInterface::class);
        $this->treeGenerator = \Phake::mock(ConfigurationInterface::class);
        $this->processor = \Phake::mock(Processor::class);
        $this->configurationFactory = \Phake::mock(ConfigurationFactoryInterface::class);
    }

    /** @test */
    public function loadConfigurationFromFile_givenFilename_configurationLoadedFromFileAndProcessedAndConfigurationClassCreatedAndReturned(): void
    {
        $loader = $this->createConfigurationLoader();
        $configurationArray = $this->givenConfigurationFileParser_parseConfigurationFile_returnsConfigurationArray();
        $processedConfiguration = $this->givenConfigurationProcessor_processConfiguration_returnsProcessedConfiguration();
        $expectedConfiguration = $this->givenConfigurationFactory_createConfiguration_returnsConfiguration();

        $configuration = $loader->loadConfigurationFromFile(self::CONFIGURATION_FILENAME);

        $this->assertConfigurationFileParser_parseConfigurationFile_isCalledOnceWithFilename(self::CONFIGURATION_FILENAME);
        $this->assertConfigurationProcessor_processConfiguration_isCalledOnceWithTreeGeneratorAndConfigurationArray(
            $configurationArray
        );
        $this->assertConfigurationFactory_createConfiguration_isCalledOnceWithArray($processedConfiguration);
        $this->assertSame($expectedConfiguration, $configuration);
    }

    private function createConfigurationLoader(): ConfigurationLoader
    {
        return new ConfigurationLoader(
            $this->fileParser,
            $this->treeGenerator,
            $this->processor,
            $this->configurationFactory
        );
    }

    private function givenConfigurationFileParser_parseConfigurationFile_returnsConfigurationArray(): array
    {
        $configurationArray = ['configuration_array'];
        \Phake::when($this->fileParser)
            ->parseConfigurationFile(\Phake::anyParameters())
            ->thenReturn($configurationArray);

        return $configurationArray;
    }

    private function givenConfigurationProcessor_processConfiguration_returnsProcessedConfiguration(): array
    {
        $processedConfiguration = ['processed_configuration'];
        \Phake::when($this->processor)
            ->processConfiguration(\Phake::anyParameters())
            ->thenReturn($processedConfiguration);

        return $processedConfiguration;
    }

    private function assertConfigurationFileParser_parseConfigurationFile_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->fileParser, \Phake::times(1))
            ->parseConfigurationFile($filename);
    }

    private function assertConfigurationProcessor_processConfiguration_isCalledOnceWithTreeGeneratorAndConfigurationArray(
        array $configurationArray
    ): void {
        \Phake::verify($this->processor, \Phake::times(1))
            ->processConfiguration($this->treeGenerator, [$configurationArray]);
    }

    private function assertConfigurationFactory_createConfiguration_isCalledOnceWithArray(
        array $processedConfiguration
    ): void {
        \Phake::verify($this->configurationFactory, \Phake::times(1))
            ->createConfiguration($processedConfiguration);
    }

    private function givenConfigurationFactory_createConfiguration_returnsConfiguration(): Configuration
    {
        $configuration = \Phake::mock(Configuration::class);
        \Phake::when($this->configurationFactory)
            ->createConfiguration(\Phake::anyParameters())
            ->thenReturn($configuration);

        return $configuration;
    }
}
