<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Processing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ThumbnailProcessingConfigurationParser;
use Strider2038\ImgCache\Imaging\Parsing\SaveOptionsConfiguratorInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Processing\SaveOptionsFactoryInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCreatorInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;

class ThumbnailProcessingConfigurationParserTest extends TestCase
{
    /** @var TransformationCreatorInterface */
    private $transformationsCreator;

    /** @var SaveOptionsFactoryInterface */
    private $saveOptionsFactory;

    /** @var SaveOptionsConfiguratorInterface */
    private $saveOptionsConfigurator;

    protected function setUp()
    {
        $this->transformationsCreator = \Phake::mock(TransformationCreatorInterface::class);
        $this->saveOptionsFactory = \Phake::mock(SaveOptionsFactoryInterface::class);
        $this->saveOptionsConfigurator = \Phake::mock(SaveOptionsConfiguratorInterface::class);
    }

    /**
     * @test
     * @param string $configuration
     * @param int $count
     * @param bool $isDefault
     * @dataProvider configurationsProvider
     */
    public function parse_givenConfigurationWithTransformations_countOfTransformationsReturned(
        string $configuration,
        int $count,
        bool $isDefault
    ): void {
        $parser = $this->createThumbnailProcessingConfigurationParser();
        $this->givenTransformationsFactory_create_returnsTransformation();
        $defaultSaveOptions = $this->givenSaveOptionsFactory_create_returns_saveOptions();

        $parsedConfiguration = $parser->parseConfiguration($configuration);

        $this->assertTransformationsCount($count, $parsedConfiguration);
        $this->assertTransformationsFactory_create_isCalled($count);
        $this->assertSaveOptionsConfigurator_updateSaveOptionsByConfiguration_isCalled(0);
        $this->verifyProcessingConfiguration($parsedConfiguration, $defaultSaveOptions, $isDefault);
    }

    /**
     * @test
     * @param string $configuration
     * @param int $count
     * @param bool $isDefault
     * @dataProvider configurationsProvider
     */
    public function getRequestConfiguration_givenConfigurationWithSaveOptions_countOfSaveOptionsConfiguratorConfigureVerified(
        string $configuration,
        int $count,
        bool $isDefault
    ): void {
        $parser = $this->createThumbnailProcessingConfigurationParser();
        $this->givenTransformationsFactory_create_returnsNull();
        $defaultSaveOptions = $this->givenSaveOptionsFactory_create_returns_saveOptions();

        $parsedConfiguration = $parser->parseConfiguration($configuration);

        $this->assertTransformationsCount(0, $parsedConfiguration);
        $this->assertTransformationsFactory_create_isCalled($count);
        $this->assertSaveOptionsConfigurator_updateSaveOptionsByConfiguration_isCalled($count);
        $this->verifyProcessingConfiguration($parsedConfiguration, $defaultSaveOptions, $isDefault);
    }

    public function configurationsProvider(): array
    {
        return [
            ['', 0, true],
            ['q95', 1, false],
            ['sz85_q7', 2, false],
        ];
    }

    private function createThumbnailProcessingConfigurationParser(): ThumbnailProcessingConfigurationParser
    {
        $parser = new ThumbnailProcessingConfigurationParser(
            $this->transformationsCreator,
            $this->saveOptionsFactory,
            $this->saveOptionsConfigurator
        );

        return $parser;
    }

    private function givenTransformationsFactory_create_returnsTransformation(): void
    {
        $transformation = \Phake::mock(TransformationInterface::class);

        \Phake::when($this->transformationsCreator)
            ->create(\Phake::anyParameters())
            ->thenReturn($transformation);
    }

    private function verifyProcessingConfiguration(
        ProcessingConfiguration $configuration,
        SaveOptions $defaultSaveOptions,
        bool $isDefault
    ): void {
        $this->assertInstanceOf(ProcessingConfiguration::class, $configuration);
        $this->assertInstanceOf(TransformationCollection::class, $configuration->getTransformations());
        $this->assertSaveOptionsFactory_create_isCalledOnce();
        $this->assertSame($defaultSaveOptions, $configuration->getSaveOptions());
        $this->assertEquals($isDefault, $configuration->isDefault());
    }

    private function assertTransformationsCount(int $count, ProcessingConfiguration $configuration): void
    {
        $transformations = $configuration->getTransformations();
        $this->assertEquals($count, $transformations->count());
    }

    private function assertTransformationsFactory_create_isCalled(int $times): void
    {
        \Phake::verify($this->transformationsCreator, \Phake::times($times))
            ->create(\Phake::anyParameters());
    }

    private function assertSaveOptionsConfigurator_updateSaveOptionsByConfiguration_isCalled(int $times): void
    {
        \Phake::verify($this->saveOptionsConfigurator, \Phake::times($times))
            ->updateSaveOptionsByConfiguration(\Phake::anyParameters());
    }

    private function givenTransformationsFactory_create_returnsNull(): void
    {
        \Phake::when($this->transformationsCreator)
            ->create(\Phake::anyParameters())
            ->thenReturn(null);
    }

    private function givenSaveOptionsFactory_create_returns_saveOptions(): SaveOptions
    {
        $defaultSaveOptions = \Phake::mock(SaveOptions::class);
        \Phake::when($this->saveOptionsFactory)->create()->thenReturn($defaultSaveOptions);

        return $defaultSaveOptions;
    }

    private function assertSaveOptionsFactory_create_isCalledOnce(): void
    {
        \Phake::verify($this->saveOptionsFactory, \Phake::times(1))->create();
    }
}
