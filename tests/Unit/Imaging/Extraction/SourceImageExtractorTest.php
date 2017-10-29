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
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKey;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;

class SourceImageExtractorTest extends TestCase
{
    private const KEY = 'a';
    private const PUBLIC_FILENAME = 'b';

    /** @var SourceKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    protected function setUp()
    {
        $this->keyParser = \Phake::mock(SourceKeyParserInterface::class);
        $this->sourceAccessor = \Phake::mock(SourceAccessorInterface::class);
    }

    /** @test */
    public function extract_imageDoesNotExistInSource_nullIsReturned(): void
    {
        $extractor = $this->createSourceImageExtractor();
        $publicFilename = self::PUBLIC_FILENAME;
        $this->givenKeyParser_parse_returnsSourceKey();
        $this->givenSourceAccessor_get_returnsNull($publicFilename);

        $extractedImage = $extractor->extract(self::KEY);

        $this->assertNull($extractedImage);
    }

    /** @test */
    public function extract_imageExistsInSource_imageIsReturned(): void
    {
        $extractor = $this->createSourceImageExtractor();
        $publicFilename = self::PUBLIC_FILENAME;
        $this->givenKeyParser_parse_returnsSourceKey();
        $image = $this->givenSourceAccessor_get_returnsImage($publicFilename);

        $extractedImage = $extractor->extract(self::KEY);

        $this->assertSame($image, $extractedImage);
    }

    private function createSourceImageExtractor(): SourceImageExtractor
    {
        return new SourceImageExtractor($this->keyParser, $this->sourceAccessor);
    }

    private function givenKeyParser_parse_returnsSourceKey(): SourceKey
    {
        $sourceKey = \Phake::mock(SourceKey::class);
        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($sourceKey);
        \Phake::when($sourceKey)->getPublicFilename()->thenReturn(self::PUBLIC_FILENAME);

        return $sourceKey;
    }

    private function givenSourceAccessor_get_returnsImage(string $publicFilename): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->sourceAccessor)->get($publicFilename)->thenReturn($image);

        return $image;
    }

    private function givenSourceAccessor_get_returnsNull(string $publicFilename): void
    {
        \Phake::when($this->sourceAccessor)->get($publicFilename)->thenReturn(null);
    }
}
