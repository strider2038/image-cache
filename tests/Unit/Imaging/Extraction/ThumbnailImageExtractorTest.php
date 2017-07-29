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
use Strider2038\ImgCache\Imaging\Extraction\SourceImageExtractor;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageExtractor;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Parsing\Processing\ProcessingConfigurationParserInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ThumbnailImageExtractorTest extends TestCase
{
    use ImageTrait, ProviderTrait;

    const KEY = 'key';
    const CONFIGURATION_STRING = 's';

    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var SourceImageExtractor */
    private $sourceImageExtractor;

    /** @var ProcessingConfigurationParserInterface */
    private $processingConfigurationParser;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    protected function setUp()
    {
        $this->keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        $this->sourceImageExtractor = \Phake::mock(SourceImageExtractor::class);
        $this->processingConfigurationParser = \Phake::mock(ProcessingConfigurationParserInterface::class);
        $this->imageProcessor = \Phake::mock(ImageProcessorInterface::class);
    }

    public function testExtract_ImageDoesNotExistInSource_NullIsReturned(): void
    {
        $extractor = $this->createThumbnailImageExtractor();
        $this->givenSourceImageExtractor_Extract_Returns(null);

        $extractedImage = $extractor->extract(self::KEY);

        $this->assertNull($extractedImage);
    }

    public function testExtract_ImageExistsInSource_ImageIsProcessedAndReturned(): void
    {
        $extractor = $this->createThumbnailImageExtractor();
        $sourceImage = $this->givenImage();
        $this->givenSourceImageExtractor_Extract_Returns($sourceImage);
        $thumbnailKey = \Phake::mock(ThumbnailKeyInterface::class);
        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($thumbnailKey);
        \Phake::when($thumbnailKey)
            ->getProcessingConfiguration()
            ->thenReturn(self::CONFIGURATION_STRING);
        $processingConfiguration = \Phake::mock(ProcessingConfigurationInterface::class);
        \Phake::when($this->processingConfigurationParser)
            ->parse(self::CONFIGURATION_STRING)
            ->thenReturn($processingConfiguration);
        $processedImage = $this->givenImage();
        \Phake::when($this->imageProcessor)
            ->process($processingConfiguration, $sourceImage)
            ->thenReturn($processedImage);

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
        \Phake::when($this->sourceImageExtractor)->exists(self::KEY)->thenReturn($expectedExists);

        $actualExists = $extractor->exists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
    }

    private function createThumbnailImageExtractor(): ThumbnailImageExtractor
    {
        $extractor = new ThumbnailImageExtractor(
            $this->keyParser,
            $this->sourceImageExtractor,
            $this->processingConfigurationParser,
            $this->imageProcessor
        );
        return $extractor;
    }

    private function givenSourceImageExtractor_Extract_Returns(?ImageInterface $sourceImage): void
    {
        \Phake::when($this->sourceImageExtractor)->extract(self::KEY)->thenReturn($sourceImage);
    }
}
