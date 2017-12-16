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
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

class SourceImageExtractorTest extends TestCase
{
    private const KEY = 'a';
    private const PUBLIC_FILENAME = 'b';

    /** @var SourceKeyParserInterface */
    private $keyParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    protected function setUp(): void
    {
        $this->keyParser = \Phake::mock(SourceKeyParserInterface::class);
        $this->storageAccessor = \Phake::mock(StorageAccessorInterface::class);
    }

    /** @test */
    public function extract_imageExistsInSource_imageIsReturned(): void
    {
        $extractor = $this->createSourceImageExtractor();
        $publicFilename = self::PUBLIC_FILENAME;
        $this->givenKeyParser_parse_returnsSourceKey();
        $image = $this->givenStorageAccessor_getImage_returnsImage($publicFilename);

        $extractedImage = $extractor->extractImage(self::KEY);

        $this->assertSame($image, $extractedImage);
    }

    private function createSourceImageExtractor(): SourceImageExtractor
    {
        return new SourceImageExtractor($this->keyParser, $this->storageAccessor);
    }

    private function givenKeyParser_parse_returnsSourceKey(): SourceKey
    {
        $sourceKey = \Phake::mock(SourceKey::class);
        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($sourceKey);
        \Phake::when($sourceKey)->getPublicFilename()->thenReturn(self::PUBLIC_FILENAME);

        return $sourceKey;
    }

    private function givenStorageAccessor_getImage_returnsImage(string $publicFilename): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->storageAccessor)->getImage($publicFilename)->thenReturn($image);

        return $image;
    }
}
