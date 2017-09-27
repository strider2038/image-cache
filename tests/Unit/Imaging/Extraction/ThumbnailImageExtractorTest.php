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

class ThumbnailImageExtractorTest extends TestCase
{
    use ImageTrait;

    private const KEY = '/publicFilename_configString.jpg';
    private const PUBLIC_FILENAME = '/publicFilename.jpg';
    private const CONFIGURATION_STRING = 'configString';

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

    /** @test */
    public function extract_imageDoesNotExistInSource_nullIsReturned(): void
    {
        $extractor = $this->createThumbnailImageExtractor();
        $this->givenKeyParser_parse_returnsThumbnailKey();
        $this->givenSourceAccessor_get_returns(null);

        $extractedImage = $extractor->extract(self::KEY);

        $this->assertNull($extractedImage);
    }

    /** @test */
    public function extract_imageExistsInSource_imageIsProcessedAndReturned(): void
    {
        $extractor = $this->createThumbnailImageExtractor();
        $this->givenKeyParser_parse_returnsThumbnailKey();
        $sourceImage = $this->givenImage();
        $this->givenSourceAccessor_get_returns($sourceImage);
        $processingConfiguration = $this->givenProcessingConfigurationParser_parse_returnsProcessingConfiguration();
        $processedImage = $this->givenImageProcessor_process_returnsProcessedImage($processingConfiguration, $sourceImage);

        $extractedImage = $extractor->extract(self::KEY);

        $this->assertSame($processedImage, $extractedImage);
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

    private function givenSourceAccessor_get_returns(?ImageInterface $sourceImage): void
    {
        \Phake::when($this->sourceAccessor)->get(self::PUBLIC_FILENAME)->thenReturn($sourceImage);
    }

    private function givenKeyParser_parse_returnsThumbnailKey(): ThumbnailKeyInterface
    {
        $thumbnailKey = \Phake::mock(ThumbnailKeyInterface::class);
        \Phake::when($thumbnailKey)->getPublicFilename()->thenReturn(self::PUBLIC_FILENAME);
        \Phake::when($thumbnailKey)->getProcessingConfiguration()->thenReturn(self::CONFIGURATION_STRING);
        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($thumbnailKey);

        return $thumbnailKey;
    }

    private function givenProcessingConfigurationParser_parse_returnsProcessingConfiguration(): ProcessingConfigurationInterface
    {
        $processingConfiguration = \Phake::mock(ProcessingConfigurationInterface::class);

        \Phake::when($this->processingConfigurationParser)
            ->parse(self::CONFIGURATION_STRING)
            ->thenReturn($processingConfiguration);

        return $processingConfiguration;
    }

    private function givenImageProcessor_process_returnsProcessedImage(
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
