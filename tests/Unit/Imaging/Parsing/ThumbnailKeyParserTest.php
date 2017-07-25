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
use Strider2038\ImgCache\Imaging\Parsing\SaveOptionsConfiguratorInterface;
use Strider2038\ImgCache\Imaging\Parsing\ThumbnailKeyParser;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailKeyParserTest extends TestCase
{
    /** @var TransformationsFactoryInterface */
    private $transformationsFactory;

    /** @var SaveOptionsConfiguratorInterface */
    private $saveOptionsConfigurator;

    protected function setUp()
    {
        $this->transformationsFactory = \Phake::mock(TransformationsFactoryInterface::class);
        $this->saveOptionsConfigurator = \Phake::mock(SaveOptionsConfiguratorInterface::class);
    }

    /**
     * @dataProvider incorrectFilenamesProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidImageException
     * @expectedExceptionCode 400
     */
    public function testGetRequestConfiguration_FilenameWithIllegalChars_ExceptionThrown(string $filename): void
    {
        $parser = $this->createThumbnailKeyParser();

        $parser->getRequestConfiguration($filename);
    }

    private function createThumbnailKeyParser(): ThumbnailKeyParser
    {
        return new ThumbnailKeyParser($this->transformationsFactory, $this->saveOptionsConfigurator);
    }

    public function incorrectFilenamesProvider(): array
    {
        return [
            [''],
            ['/'],
            ['file .jpg'],
            ['кириллица.jpg'],
            ['/path/\1'],
            ['file.err'],
            ['../file.jpg'],
            ['.../i.jpg'],
            ['f../i.jpg'],
            ['/../file.jpg'],
            ['dir.name/f.jpeg'],
            ['/.dir/f.jpg'],
        ];
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

        $requestConfiguration = $parser->getRequestConfiguration($sourceFilename);

        $extractionRequest = $requestConfiguration->getExtractionRequest();
        $this->assertEquals($destinationFilename, $extractionRequest->getFilename());
        $this->verifyRequestConfiguration($requestConfiguration);
    }

    private function givenTransformationsFactoryReturnsTransformation(): void
    {
        $transformation = \Phake::mock(TransformationInterface::class);
        \Phake::when($this->transformationsFactory)
            ->create(\Phake::anyParameters())
            ->thenReturn($transformation);
    }

    private function verifyRequestConfiguration($requestConfiguration): void
    {
        $this->assertInstanceOf(TransformationsCollection::class, $requestConfiguration->getTransformations());
        $this->assertInstanceOf(SaveOptions::class, $requestConfiguration->getSaveOptions());
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

        $requestConfiguration = $parser->getRequestConfiguration($filename);

        $this->assertTransformationsCount($count, $requestConfiguration);
        $this->assertTransformationsFactoryCreateCalled($count);
        $this->assertSaveOptionsConfiguratorConfigureCalled(0);
        $this->verifyRequestConfiguration($requestConfiguration);
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

    /**
     * @dataProvider filenamesWithTransformationsProvider
     */
    public function testGetRequestConfiguration_Filename_CountOfSaveOptionsConfiguratorConfigureVerified(
        string $filename,
        int $count
    ): void {
        $parser = $this->createThumbnailKeyParser();
        $this->givenTransformationsFactoryReturnsNull();

        $requestConfiguration = $parser->getRequestConfiguration($filename);

        $this->assertTransformationsCount(0, $requestConfiguration);
        $this->assertTransformationsFactoryCreateCalled($count);
        $this->assertSaveOptionsConfiguratorConfigureCalled($count);
        $this->verifyRequestConfiguration($requestConfiguration);
    }

    private function givenTransformationsFactoryReturnsNull(): void
    {
        \Phake::when($this->transformationsFactory)
            ->create(\Phake::anyParameters())
            ->thenReturn(null);
    }

    public function filenamesWithTransformationsProvider(): array
    {
        return [
            ['i.jpg', 0],
            ['i_q95.jpg', 1],
            ['i_sz85_q7.jpg', 2],
        ];
    }


}
