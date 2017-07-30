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
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageExtractor;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ProcessingConfigurationParserInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ThumbnailImageExtractorTest extends TestCase
{
    use ImageTrait, ProviderTrait;

    const KEY = '/publicFilename_configString.jpg';
    const PUBLIC_FILENAME = '/publicFilename.jpg';
    const CONFIGURATION_STRING = 'configString';

    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    protected function setUp()
    {
        $this->keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        $this->sourceAccessor = \Phake::mock(SourceAccessorInterface::class);
        $this->processingConfigurationParser = \Phake::mock(ProcessingConfigurationParserInterface::class);
        $this->imageProcessor = \Phake::mock(ImageProcessorInterface::class);
    }

    public function testExtract_ImageDoesNotExistInSource_NullIsReturned(): void
    {
        $extractor = $this->createThumbnailImageExtractor();
        $this->givenKeyParser_Parse_Returns_ThumbnailKey();
        $this->givenSourceAccessor_Get_Returns(null);

        $extractedImage = $extractor->extract(self::KEY);

        $this->assertNull($extractedImage);
    }

    public function testExtract_ImageExistsInSource_ImageIsProcessedAndReturned(): void
    {
        $extractor = $this->createThumbnailImageExtractor();
        $this->givenKeyParser_Parse_Returns_ThumbnailKey();
        $sourceImage = $this->givenImage();
        $this->givenSourceAccessor_Get_Returns($sourceImage);
        $processingConfiguration = $this->givenProcessingConfigurationParser_Parse_Returns_ProcessingConfiguration();
        $processedImage = $this->givenImageProcessor_Process_Returns_ProcessedImage($processingConfiguration, $sourceImage);

        $extractedImage = $extractor->extract(self::KEY);

        $this->assertSame($processedImage, $extractedImage);
    }

    /**
     * @param bool $expectedExists
     * @dataProvider boolValuesProvider
     */
    public function testExists_SourceImageExtractorExistsReturnsBool_BoolIsReturned(bool $expectedExists): void
    {
        $extractor = $this->createThumbnailImageExtractor();
        $this->givenKeyParser_Parse_Returns_ThumbnailKey();
        $this->givenSourceAccessor_Exists_Returns($expectedExists);

        $actualExists = $extractor->exists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
    }

    private function createThumbnailImageExtractor(): ThumbnailImageExtractor
    {
        $extractor = new ThumbnailImageExtractor(
            $this->keyParser,
            $this->sourceAccessor,
            $this->processingConfigurationParser,
            $this->imageProcessor
        );
        return $extractor;
    }

    private function givenSourceAccessor_Get_Returns(?ImageInterface $sourceImage): void
    {
        \Phake::when($this->sourceAccessor)->get(self::PUBLIC_FILENAME)->thenReturn($sourceImage);
    }

    private function givenSourceAccessor_Exists_Returns(bool $value): void
    {
        \Phake::when($this->sourceAccessor)->exists(self::PUBLIC_FILENAME)->thenReturn($value);
    }

    private function givenKeyParser_Parse_Returns_ThumbnailKey(): ThumbnailKeyInterface
    {
        $thumbnailKey = \Phake::mock(ThumbnailKeyInterface::class);

        \Phake::when($thumbnailKey)->getPublicFilename()->thenReturn(self::PUBLIC_FILENAME);

        \Phake::when($thumbnailKey)->getProcessingConfiguration()->thenReturn(self::CONFIGURATION_STRING);

        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($thumbnailKey);

        return $thumbnailKey;
    }

    private function givenProcessingConfigurationParser_Parse_Returns_ProcessingConfiguration(): ProcessingConfigurationInterface
    {
        $processingConfiguration = \Phake::mock(ProcessingConfigurationInterface::class);

        \Phake::when($this->processingConfigurationParser)
            ->parse(self::CONFIGURATION_STRING)
            ->thenReturn($processingConfiguration);

        return $processingConfiguration;
    }

    private function givenImageProcessor_Process_Returns_ProcessedImage(
        ProcessingConfigurationInterface $processingConfiguration,
        ImageInterface $sourceImage
    ): ImageInterface {
        $processedImage = $this->givenImage();

        \Phake::when($this->imageProcessor)
            ->process($processingConfiguration, $sourceImage)
            ->thenReturn($processedImage);

        return $processedImage;
    }
}
