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
use Strider2038\ImgCache\Configuration\ApplicationConfiguration;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationFactoryInterface;
use Strider2038\ImgCache\Configuration\ConfigurationLoader;
use Strider2038\ImgCache\Utility\ConfigurationFileParserInterface;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationLoaderTest extends TestCase
{
    private const CONFIGURATION_FILENAME = 'config/parameters.yml';

    /** @var ConfigurationFileParserInterface */
    private $configurationFileParser;

    /** @var Processor */
    private $configurationProcessor;

    /** @var ConfigurationFactoryInterface */
    private $configurationFactory;

    protected function setUp(): void
    {
        $this->configurationFileParser = \Phake::mock(ConfigurationFileParserInterface::class);
        $this->configurationProcessor = \Phake::mock(Processor::class);
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
        $this->assertConfigurationProcessor_processConfiguration_isCalledOnceWithInstanceOfApplicationConfigurationAndConfigurationArray(
            ApplicationConfiguration::class,
            $configurationArray
        );
        $this->assertConfigurationFactory_createConfiguration_isCalledOnceWithArray($processedConfiguration);
        $this->assertSame($expectedConfiguration, $configuration);
    }

    private function createConfigurationLoader(): ConfigurationLoader
    {
        return new ConfigurationLoader(
            $this->configurationFileParser,
            $this->configurationProcessor,
            $this->configurationFactory
        );
    }

    private function givenConfigurationFileParser_parseConfigurationFile_returnsConfigurationArray(): array
    {
        $configurationArray = ['configuration_array'];
        \Phake::when($this->configurationFileParser)
            ->parseConfigurationFile(\Phake::anyParameters())
            ->thenReturn($configurationArray);

        return $configurationArray;
    }

    private function givenConfigurationProcessor_processConfiguration_returnsProcessedConfiguration(): array
    {
        $processedConfiguration = ['processed_configuration'];
        \Phake::when($this->configurationProcessor)
            ->processConfiguration(\Phake::anyParameters())
            ->thenReturn($processedConfiguration);

        return $processedConfiguration;
    }

    private function assertConfigurationFileParser_parseConfigurationFile_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->configurationFileParser, \Phake::times(1))
            ->parseConfigurationFile($filename);
    }

    private function assertConfigurationProcessor_processConfiguration_isCalledOnceWithInstanceOfApplicationConfigurationAndConfigurationArray(
        string $configurationClassName,
        array $configurationArray
    ): void {
        \Phake::verify($this->configurationProcessor, \Phake::times(1))
            ->processConfiguration(\Phake::capture($configurationClass), [$configurationArray]);
        $this->assertInstanceOf($configurationClassName, $configurationClass);
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
