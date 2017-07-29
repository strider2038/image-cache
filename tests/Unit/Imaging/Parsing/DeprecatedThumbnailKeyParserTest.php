<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging\Parsing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\DeprecatedThumbnailKeyParser;
use Strider2038\ImgCache\Imaging\Parsing\SaveOptionsConfiguratorInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Processing\SaveOptionsFactoryInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class DeprecatedThumbnailKeyParserTest extends TestCase
{
    /** @var TransformationsFactoryInterface */
    private $transformationsFactory;

    /** @var SaveOptionsFactoryInterface */
    private $saveOptionsFactory;

    /** @var SaveOptionsConfiguratorInterface */
    private $saveOptionsConfigurator;

    protected function setUp()
    {
        $this->markTestSkipped();
        $this->transformationsFactory = \Phake::mock(TransformationsFactoryInterface::class);
        $this->saveOptionsFactory = \Phake::mock(SaveOptionsFactoryInterface::class);
        $this->saveOptionsConfigurator = \Phake::mock(SaveOptionsConfiguratorInterface::class);
    }

    /**
     * @dataProvider filenamesProvider
     */
    public function testGetRequestConfiguration_Filename_ModifiedFilenameSetToExtractionRequest(
        string $sourceFilename,
        string $destinationFilename
    ): void {
        $parser = $this->createThumbnailKeyParser();
        $this->givenTransformationsFactoryReturnsTransformation();
        $defaultSaveOptions = $this->givenDefaultSaveOptions();

        $requestConfiguration = $parser->getRequestConfiguration($sourceFilename);

        $extractionRequest = $requestConfiguration->getExtractionRequest();
        $this->assertEquals($destinationFilename, $extractionRequest->getFilename());
        $this->verifyRequestConfiguration($requestConfiguration, $defaultSaveOptions);
    }

    public function filenamesProvider(): array
    {
        return [
            ['./f.jpeg', '/f.jpeg'],
            ['/./f.jpeg', '/f.jpeg'],
            ['Img.jpg', '/Img.jpg'],
            ['/path/image.JPG', '/path/image.JPG'],
            ['/path/image_sz80x100.jpg', '/path/image.jpg'],
            ['_root/i_q95.jpeg', '/_root/i.jpeg'],
        ];
    }

    /**
     * @dataProvider filenamesWithTransformationsProvider
     */
    public function testGetRequestConfiguration_Filename_CountOfTransformationsReturned(
        string $filename,
        int $count
    ): void {
        $parser = $this->createThumbnailKeyParser();
        $this->givenTransformationsFactoryReturnsTransformation();
        $defaultSaveOptions = $this->givenDefaultSaveOptions();

        $requestConfiguration = $parser->getRequestConfiguration($filename);

        $this->assertTransformationsCount($count, $requestConfiguration);
        $this->assertTransformationsFactoryCreateCalled($count);
        $this->assertSaveOptionsConfiguratorConfigureCalled(0);
        $this->verifyRequestConfiguration($requestConfiguration, $defaultSaveOptions);
    }

    /**
     * @dataProvider filenamesWithTransformationsProvider
     */
    public function testGetRequestConfiguration_Filename_CountOfSaveOptionsConfiguratorConfigureVerified(
        string $filename,
        int $count
    ): void {
        $parser = $this->createThumbnailKeyParser();
        $this->givenTransformationsFactoryReturnsNull();
        $defaultSaveOptions = $this->givenDefaultSaveOptions();

        $requestConfiguration = $parser->getRequestConfiguration($filename);

        $this->assertTransformationsCount(0, $requestConfiguration);
        $this->assertTransformationsFactoryCreateCalled($count);
        $this->assertSaveOptionsConfiguratorConfigureCalled($count);
        $this->verifyRequestConfiguration($requestConfiguration, $defaultSaveOptions);
    }

    public function filenamesWithTransformationsProvider(): array
    {
        return [
            ['i.jpg', 0],
            ['i_q95.jpg', 1],
            ['i_sz85_q7.jpg', 2],
        ];
    }

    private function createThumbnailKeyParser(): DeprecatedThumbnailKeyParser
    {
        $thumbnailKeyParser = new DeprecatedThumbnailKeyParser(
            $this->transformationsFactory,
            $this->saveOptionsFactory,
            $this->saveOptionsConfigurator
        );

        return $thumbnailKeyParser;
    }

    private function givenTransformationsFactoryReturnsTransformation(): void
    {
        $transformation = \Phake::mock(TransformationInterface::class);
        \Phake::when($this->transformationsFactory)
            ->create(\Phake::anyParameters())
            ->thenReturn($transformation);
    }

    private function verifyRequestConfiguration(
        ThumbnailRequestConfigurationInterface $requestConfiguration,
        SaveOptions $defaultSaveOptions
    ): void {
        $this->assertInstanceOf(TransformationsCollection::class, $requestConfiguration->getTransformations());
        $this->assertSaveOptionsFactoryCreateIsCalledOnce();
        $this->assertSame($defaultSaveOptions, $requestConfiguration->getSaveOptions());
    }

    private function assertTransformationsCount(int $count, $requestConfiguration): void
    {
        $transformations = $requestConfiguration->getTransformations();
        $this->assertEquals($count, $transformations->count());
    }

    private function assertTransformationsFactoryCreateCalled(int $times): void
    {
        \Phake::verify($this->transformationsFactory, \Phake::times($times))
            ->create(\Phake::anyParameters());
    }

    private function assertSaveOptionsConfiguratorConfigureCalled(int $times): void
    {
        \Phake::verify($this->saveOptionsConfigurator, \Phake::times($times))
            ->configure(\Phake::anyParameters());
    }

    private function givenTransformationsFactoryReturnsNull(): void
    {
        \Phake::when($this->transformationsFactory)
            ->create(\Phake::anyParameters())
            ->thenReturn(null);
    }

    private function givenDefaultSaveOptions(): SaveOptions
    {
        $defaultSaveOptions = \Phake::mock(SaveOptions::class);

        \Phake::when($this->saveOptionsFactory)
            ->create()
            ->thenReturn($defaultSaveOptions);

        return $defaultSaveOptions;
    }

    private function assertSaveOptionsFactoryCreateIsCalledOnce(): void
    {
        \Phake::verify($this->saveOptionsFactory, \Phake::times(1))->create();
    }

}
