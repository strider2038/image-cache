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
use Strider2038\ImgCache\Imaging\Extraction\ExtractedImageInterface;
use Strider2038\ImgCache\Imaging\Extraction\FileExtractionRequestInterface;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImage;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageExtractor;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\ThumbnailKeyParserFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\FileSourceInterface;

class ThumbnailImageExtractorTest extends TestCase
{
    const KEY = 'key';

    /** @var FileSourceInterface */
    private $source;

    /** @var ThumbnailKeyParserFactoryInterface */
    private $keyParserFactory;

    /** @var ThumbnailImageFactoryInterface */
    private $thumbnailImageFactory;

    protected function setUp()
    {
        $this->source = \Phake::mock(FileSourceInterface::class);
        $this->keyParserFactory = \Phake::mock(ThumbnailKeyParserFactoryInterface::class);
        $this->thumbnailImageFactory = \Phake::mock(ThumbnailImageFactoryInterface::class);
    }

    public function testExtract_SourceImageNotFound_NullIsReturned(): void
    {
        $imageExtractor = new ThumbnailImageExtractor($this->source, $this->keyParserFactory, $this->thumbnailImageFactory);
        $keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        \Phake::when($this->keyParserFactory)->create(self::KEY)->thenReturn($keyParser);
        $extractionRequest = \Phake::mock(FileExtractionRequestInterface::class);
        \Phake::when($keyParser)->getExtractionRequest()->thenReturn($extractionRequest);
        \Phake::when($this->source)->get($extractionRequest)->thenReturn(null);

        $extractedImage = $imageExtractor->extract(self::KEY);

        $this->assertNull($extractedImage);
    }

    public function testExtract_SourceImageFoundAndNoTransformationsNeeded_SourceImageIsReturned(): void
    {
        $imageExtractor = new ThumbnailImageExtractor($this->source, $this->keyParserFactory, $this->thumbnailImageFactory);
        $keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        \Phake::when($this->keyParserFactory)->create(self::KEY)->thenReturn($keyParser);
        $extractionRequest = \Phake::mock(FileExtractionRequestInterface::class);
        \Phake::when($keyParser)->getExtractionRequest()->thenReturn($extractionRequest);
        $sourceImage = \Phake::mock(ExtractedImageInterface::class);
        \Phake::when($this->source)->get($extractionRequest)->thenReturn($sourceImage);
        \Phake::when($keyParser)->hasTransformations()->thenReturn(false);

        $extractedImage = $imageExtractor->extract(self::KEY);

        $this->assertInstanceOf(ExtractedImageInterface::class, $extractedImage);
    }

    public function testExtract_SourceImageFoundAndTransformationsNeeded_ThumbnailImageIsReturned(): void
    {
        $imageExtractor = new ThumbnailImageExtractor($this->source, $this->keyParserFactory, $this->thumbnailImageFactory);
        $keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        \Phake::when($this->keyParserFactory)->create(self::KEY)->thenReturn($keyParser);
        $extractionRequest = \Phake::mock(FileExtractionRequestInterface::class);
        \Phake::when($keyParser)->getExtractionRequest()->thenReturn($extractionRequest);
        $sourceImage = \Phake::mock(ExtractedImageInterface::class);
        \Phake::when($this->source)->get($extractionRequest)->thenReturn($sourceImage);
        \Phake::when($keyParser)->hasTransformations()->thenReturn(true);
        $thumbnailImage = \Phake::mock(ThumbnailImage::class);
        \Phake::when($this->thumbnailImageFactory)->create($keyParser, $sourceImage)->thenReturn($thumbnailImage);

        $extractedImage = $imageExtractor->extract(self::KEY);

        $this->assertInstanceOf(ExtractedImageInterface::class, $extractedImage);
        $this->assertInstanceOf(ThumbnailImage::class, $extractedImage);
    }

    /** @dataProvider getExistsValues */
    public function testExists_SourceImageExistsCalled_BoolIsReturned(bool $expectedExists): void
    {
        $imageExtractor = new ThumbnailImageExtractor($this->source, $this->keyParserFactory, $this->thumbnailImageFactory);
        $keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        \Phake::when($this->keyParserFactory)->create(self::KEY)->thenReturn($keyParser);
        $extractionRequest = \Phake::mock(FileExtractionRequestInterface::class);
        \Phake::when($keyParser)->getExtractionRequest()->thenReturn($extractionRequest);
        \Phake::when($this->source)->exists($extractionRequest)->thenReturn($expectedExists);

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
}
