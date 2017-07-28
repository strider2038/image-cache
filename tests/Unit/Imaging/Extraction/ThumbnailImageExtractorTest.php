<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Extraction;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Extraction\Request\FileExtractionRequestInterface;
use Strider2038\ImgCache\Imaging\Extraction\Request\ThumbnailRequestConfigurationInterface;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageExtractor;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Parsing\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\FilesystemSourceInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;

class ThumbnailImageExtractorTest extends TestCase
{
    use ImageTrait;

    const KEY = 'key';

    /** @var FilesystemSourceInterface */
    private $source;

    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var ThumbnailImageFactoryInterface */
    private $thumbnailImageFactory;

    protected function setUp()
    {
        $this->markTestSkipped();

        $this->source = \Phake::mock(FilesystemSourceInterface::class);
        $this->keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        $this->thumbnailImageFactory = \Phake::mock(ThumbnailImageFactoryInterface::class);
    }

    public function testExtract_SourceImageNotFound_NullIsReturned(): void
    {
        $imageExtractor = $this->createThumbnailImageExtractor();
        $requestConfiguration = $this->givenThumbnailRequestConfiguration();
        $this->givenKeyParser_GetRequestConfiguration_Returns($requestConfiguration);
        $extractionRequest = $this->givenFileExtractionRequest();
        $this->givenRequestConfiguration_GetExtractionRequest_Returns($requestConfiguration, $extractionRequest);
        $this->givenSource_Get_Returns($extractionRequest, null);

        $extractedImage = $imageExtractor->extract(self::KEY);

        $this->assertNull($extractedImage);
    }

    public function testExtract_SourceImageFoundAndNoTransformationsNeeded_SourceImageIsReturned(): void
    {
        $imageExtractor = $this->createThumbnailImageExtractor();
        $requestConfiguration = $this->givenThumbnailRequestConfiguration();
        $this->givenKeyParser_GetRequestConfiguration_Returns($requestConfiguration);
        $extractionRequest = $this->givenFileExtractionRequest();
        $this->givenRequestConfiguration_GetExtractionRequest_Returns($requestConfiguration, $extractionRequest);
        $sourceImage = $this->givenImage();
        $this->givenSource_Get_Returns($extractionRequest, $sourceImage);
        $this->givenRequestConfiguration_HasTransformation_Returns($requestConfiguration, false);

        $extractedImage = $imageExtractor->extract(self::KEY);

        $this->assertInstanceOf(ImageInterface::class, $extractedImage);
        $this->assertSame($sourceImage, $extractedImage);
    }

    public function testExtract_SourceImageFoundAndTransformationsNeeded_ThumbnailImageIsReturned(): void
    {
        $imageExtractor = $this->createThumbnailImageExtractor();
        $requestConfiguration = $this->givenThumbnailRequestConfiguration();
        $this->givenKeyParser_GetRequestConfiguration_Returns($requestConfiguration);
        $extractionRequest = $this->givenFileExtractionRequest();
        $this->givenRequestConfiguration_GetExtractionRequest_Returns($requestConfiguration, $extractionRequest);
        $sourceImage = $this->givenImage();
        $this->givenSource_Get_Returns($extractionRequest, $sourceImage);
        $this->givenRequestConfiguration_HasTransformation_Returns($requestConfiguration, true);
        $thumbnailImage = $this->givenProcessingImage();
        $this->givenThumbnailImageFactory_Create_Returns($requestConfiguration, $sourceImage, $thumbnailImage);

        $extractedImage = $imageExtractor->extract(self::KEY);

        $this->assertInstanceOf(ImageInterface::class, $extractedImage);
    }

    /** @dataProvider getExistsValues */
    public function testExists_SourceImageExistsCalled_BoolIsReturned(bool $expectedExists): void
    {
        $imageExtractor = $this->createThumbnailImageExtractor();
        $requestConfiguration = $this->givenThumbnailRequestConfiguration();
        $this->givenKeyParser_GetRequestConfiguration_Returns($requestConfiguration);
        $extractionRequest = $this->givenFileExtractionRequest();
        $this->givenRequestConfiguration_GetExtractionRequest_Returns($requestConfiguration, $extractionRequest);
        $this->givenSource_Exists_Returns($extractionRequest, $expectedExists);

        $actualExists = $imageExtractor->exists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
    }

    public function getExistsValues(): array
    {
        return [
            [true],
            [false],
        ];
    }

    private function createThumbnailImageExtractor(): ThumbnailImageExtractor
    {
        $imageExtractor = new ThumbnailImageExtractor($this->source, $this->keyParser, $this->thumbnailImageFactory);

        return $imageExtractor;
    }

    private function givenThumbnailRequestConfiguration(): ThumbnailRequestConfigurationInterface
    {
        $requestConfiguration = \Phake::mock(ThumbnailRequestConfigurationInterface::class);

        return $requestConfiguration;
    }

    private function givenKeyParser_GetRequestConfiguration_Returns(
        ThumbnailRequestConfigurationInterface $requestConfiguration
    ): void {
        \Phake::when($this->keyParser)
            ->getRequestConfiguration(self::KEY)
            ->thenReturn($requestConfiguration);
    }

    private function givenFileExtractionRequest(): FileExtractionRequestInterface
    {
        $extractionRequest = \Phake::mock(FileExtractionRequestInterface::class);

        return $extractionRequest;
    }

    private function givenRequestConfiguration_GetExtractionRequest_Returns(
        ThumbnailRequestConfigurationInterface $requestConfiguration,
        FileExtractionRequestInterface$extractionRequest
    ): void {
        \Phake::when($requestConfiguration)
            ->getExtractionRequest()
            ->thenReturn($extractionRequest);
    }

    private function givenSource_Get_Returns(
        FileExtractionRequestInterface $extractionRequest,
        ?ImageInterface $sourceImage
    ): void {
        \Phake::when($this->source)
            ->get($extractionRequest)
            ->thenReturn($sourceImage);
    }

    private function givenImage(): ImageInterface
    {
        $sourceImage = \Phake::mock(ImageInterface::class);

        return $sourceImage;
    }

    private function givenRequestConfiguration_HasTransformation_Returns(
        ThumbnailRequestConfigurationInterface $requestConfiguration,
        bool $value
    ): void {
        \Phake::when($requestConfiguration)
            ->hasTransformations()
            ->thenReturn($value);
    }

    private function givenThumbnailImageFactory_Create_Returns(
        ThumbnailRequestConfigurationInterface $requestConfiguration,
        ImageInterface $sourceImage,
        ImageInterface $thumbnailImage
    ): void {
        \Phake::when($this->thumbnailImageFactory)
            ->create($requestConfiguration, $sourceImage)
            ->thenReturn($thumbnailImage);
    }

    private function givenSource_Exists_Returns(
        FileExtractionRequestInterface $extractionRequest,
        bool $expectedExists
    ): void {
        \Phake::when($this->source)
            ->exists($extractionRequest)
            ->thenReturn($expectedExists);
    }
}
