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
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Processing\DeprecatedImageProcessorInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;

class ThumbnailImageExtractorTest extends TestCase
{
    use ImageTrait;

    private const KEY = '/publicFilename_configString.jpg';
    private const PUBLIC_FILENAME = '/publicFilename.jpg';

    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    /** @var DeprecatedImageProcessorInterface */
    private $imageProcessor;

    protected function setUp()
    {
        $this->keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        $this->sourceAccessor = \Phake::mock(SourceAccessorInterface::class);
        $this->imageProcessor = \Phake::mock(DeprecatedImageProcessorInterface::class);
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
        $thumbnailKey = $this->givenKeyParser_parse_returnsThumbnailKey();
        $processingConfiguration = $this->givenThumbnailKey_getProcessingConfiguration_returns($thumbnailKey);
        $sourceImage = $this->givenImage();
        $this->givenSourceAccessor_get_returns($sourceImage);
        $processedImage = $this->givenImageProcessor_process_returnsProcessedImage($processingConfiguration, $sourceImage);

        $extractedImage = $extractor->extract(self::KEY);

        $this->assertSame($processedImage, $extractedImage);
    }

    private function createThumbnailImageExtractor(): ThumbnailImageExtractor
    {
        $extractor = new ThumbnailImageExtractor(
            $this->keyParser,
            $this->sourceAccessor,
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

        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($thumbnailKey);

        return $thumbnailKey;
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

    private function givenThumbnailKey_getProcessingConfiguration_returns($thumbnailKey): ProcessingConfigurationInterface
    {
        $processingConfiguration = \Phake::mock(ProcessingConfigurationInterface::class);
        \Phake::when($thumbnailKey)->getProcessingConfiguration()->thenReturn($processingConfiguration);

        return $processingConfiguration;
    }
}
