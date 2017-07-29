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
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Processing\SaveOptionsFactoryInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsFactoryInterface;

class ThumbnailProcessingConfigurationParserTest extends TestCase
{
    /** @var TransformationsFactoryInterface */
    private $transformationsFactory;

    /** @var SaveOptionsFactoryInterface */
    private $saveOptionsFactory;

    /** @var SaveOptionsConfiguratorInterface */
    private $saveOptionsConfigurator;

    protected function setUp()
    {
        $this->transformationsFactory = \Phake::mock(TransformationsFactoryInterface::class);
        $this->saveOptionsFactory = \Phake::mock(SaveOptionsFactoryInterface::class);
        $this->saveOptionsConfigurator = \Phake::mock(SaveOptionsConfiguratorInterface::class);
    }

    /**
     * @dataProvider configurationsProvider
     */
    public function testParse_GivenConfigurationWithTransformations_CountOfTransformationsReturned(
        string $configuration,
        int $count
    ): void {
        $parser = $this->createThumbnailProcessingConfigurationParser();
        $this->givenTransformationsFactory_Create_Returns_Transformation();
        $defaultSaveOptions = $this->givenSaveOptionsFactory_Create_Returns_SaveOptions();

        $parsedConfiguration = $parser->parse($configuration);

        $this->assertTransformationsCount($count, $parsedConfiguration);
        $this->assertTransformationsFactory_Create_IsCalled($count);
        $this->assertSaveOptionsConfigurator_Configure_IsCalled(0);
        $this->verifyProcessingConfiguration($parsedConfiguration, $defaultSaveOptions);
    }

    /**
     * @dataProvider configurationsProvider
     */
    public function testGetRequestConfiguration_GivenConfigurationWithSaveOptions_CountOfSaveOptionsConfiguratorConfigureVerified(
        string $configuration,
        int $count
    ): void {
        $parser = $this->createThumbnailProcessingConfigurationParser();
        $this->givenTransformationsFactory_Create_Returns_Null();
        $defaultSaveOptions = $this->givenSaveOptionsFactory_Create_Returns_SaveOptions();

        $parsedConfiguration = $parser->parse($configuration);

        $this->assertTransformationsCount(0, $parsedConfiguration);
        $this->assertTransformationsFactory_Create_IsCalled($count);
        $this->assertSaveOptionsConfigurator_Configure_IsCalled($count);
        $this->verifyProcessingConfiguration($parsedConfiguration, $defaultSaveOptions);
    }

    public function configurationsProvider(): array
    {
        return [
            ['', 0],
            ['q95', 1],
            ['sz85_q7', 2],
        ];
    }

    private function createThumbnailProcessingConfigurationParser(): ThumbnailProcessingConfigurationParser
    {
        $parser = new ThumbnailProcessingConfigurationParser(
            $this->transformationsFactory,
            $this->saveOptionsFactory,
            $this->saveOptionsConfigurator
        );

        return $parser;
    }

    private function givenTransformationsFactory_Create_Returns_Transformation(): void
    {
        $transformation = \Phake::mock(TransformationInterface::class);

        \Phake::when($this->transformationsFactory)
            ->create(\Phake::anyParameters())
            ->thenReturn($transformation);
    }

    private function verifyProcessingConfiguration(
        ProcessingConfigurationInterface $configuration,
        SaveOptions $defaultSaveOptions
    ): void {
        $this->assertInstanceOf(ProcessingConfiguration::class, $configuration);
        $this->assertInstanceOf(TransformationsCollection::class, $configuration->getTransformations());
        $this->assertSaveOptionsFactory_Create_IsCalledOnce();
        $this->assertSame($defaultSaveOptions, $configuration->getSaveOptions());
    }

    private function assertTransformationsCount(int $count, ProcessingConfigurationInterface $configuration): void
    {
        $transformations = $configuration->getTransformations();
        $this->assertEquals($count, $transformations->count());
    }

    private function assertTransformationsFactory_Create_IsCalled(int $times): void
    {
        \Phake::verify($this->transformationsFactory, \Phake::times($times))
            ->create(\Phake::anyParameters());
    }

    private function assertSaveOptionsConfigurator_Configure_IsCalled(int $times): void
    {
        \Phake::verify($this->saveOptionsConfigurator, \Phake::times($times))
            ->configure(\Phake::anyParameters());
    }

    private function givenTransformationsFactory_Create_Returns_Null(): void
    {
        \Phake::when($this->transformationsFactory)
            ->create(\Phake::anyParameters())
            ->thenReturn(null);
    }

    private function givenSaveOptionsFactory_Create_Returns_SaveOptions(): SaveOptions
    {
        $defaultSaveOptions = \Phake::mock(SaveOptions::class);

        \Phake::when($this->saveOptionsFactory)
            ->create()
            ->thenReturn($defaultSaveOptions);

        return $defaultSaveOptions;
    }

    private function assertSaveOptionsFactory_Create_IsCalledOnce(): void
    {
        \Phake::verify($this->saveOptionsFactory, \Phake::times(1))->create();
    }
}
