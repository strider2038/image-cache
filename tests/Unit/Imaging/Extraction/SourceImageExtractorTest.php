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
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class SourceImageExtractorTest extends TestCase
{
    use ImageTrait, ProviderTrait;

    const KEY = 'a';
    const PUBLIC_FILENAME = 'b';

    /** @var SourceKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    protected function setUp()
    {
        $this->keyParser = \Phake::mock(SourceKeyParserInterface::class);
        $this->sourceAccessor = \Phake::mock(SourceAccessorInterface::class);
    }

    public function testExtract_ImageDoesNotExistInSource_NullIsReturned(): void
    {
        $extractor = $this->createSourceImageExtractor();
        $publicFilename = self::PUBLIC_FILENAME;
        $this->givenPublicFilenameFoundByKey(self::KEY, $publicFilename);
        $this->givenSourceAccessor_Get_Returns($publicFilename, null);

        $extractedImage = $extractor->extract(self::KEY);

        $this->assertNull($extractedImage);
    }

    public function testExtract_ImageExistsInSource_ImageIsReturned(): void
    {
        $extractor = $this->createSourceImageExtractor();
        $publicFilename = self::PUBLIC_FILENAME;
        $this->givenPublicFilenameFoundByKey(self::KEY, $publicFilename);
        $image = $this->givenImage();
        $this->givenSourceAccessor_Get_Returns($publicFilename, $image);

        $extractedImage = $extractor->extract(self::KEY);

        $this->assertInstanceOf(ImageInterface::class, $extractedImage);
        $this->assertSame($image, $extractedImage);
    }

    /**
     * @param bool $expectedExists
     * @dataProvider boolValuesProvider
     */
    public function testExtract_SourceAccessorExistsReturnBool_BoolIsReturned(bool $expectedExists): void
    {
        $extractor = $this->createSourceImageExtractor();
        $publicFilename = self::PUBLIC_FILENAME;
        $this->givenPublicFilenameFoundByKey(self::KEY, $publicFilename);
        $this->givenSourceAccessor_Exists_Returns($publicFilename, $expectedExists);

        $actualExists = $extractor->exists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
    }

    private function createSourceImageExtractor(): SourceImageExtractor
    {
        $extractor = new SourceImageExtractor($this->keyParser, $this->sourceAccessor);

        return $extractor;
    }

    private function givenPublicFilenameFoundByKey(string $key, string $publicFilename): void
    {
        $sourceKey = \Phake::mock(SourceKeyInterface::class);
        \Phake::when($this->keyParser)->parse($key)->thenReturn($sourceKey);
        \Phake::when($sourceKey)->getPublicFilename()->thenReturn($publicFilename);
    }

    private function givenSourceAccessor_Get_Returns(string $publicFilename, ?ImageInterface $image): void
    {
        \Phake::when($this->sourceAccessor)->get($publicFilename)->thenReturn($image);
    }

    private function givenSourceAccessor_Exists_Returns(string $publicFilename, bool $value): void
    {
        \Phake::when($this->sourceAccessor)->exists($publicFilename)->thenReturn($value);
    }
}
