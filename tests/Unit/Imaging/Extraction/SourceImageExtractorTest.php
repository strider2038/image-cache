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
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

class SourceImageExtractorTest extends TestCase
{
    private const FILENAME = 'a';
    private const PARSED_FILENAME_VALUE = 'b';

    /** @var PlainFilenameParserInterface */
    private $filenameParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    protected function setUp(): void
    {
        $this->filenameParser = \Phake::mock(PlainFilenameParserInterface::class);
        $this->storageAccessor = \Phake::mock(StorageAccessorInterface::class);
    }

    /** @test */
    public function getProcessedImage_imageExistsInSource_imageIsReturned(): void
    {
        $extractor = $this->createSourceImageExtractor();
        $parsedFilename = self::PARSED_FILENAME_VALUE;
        $this->givenFilenameParser_getParsedFilename_returnsPlainFilename();
        $image = $this->givenStorageAccessor_getImage_returnsImage($parsedFilename);

        $extractedImage = $extractor->getProcessedImage(self::FILENAME);

        $this->assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertSame($image, $extractedImage);
    }

    private function createSourceImageExtractor(): SourceImageExtractor
    {
        return new SourceImageExtractor($this->filenameParser, $this->storageAccessor);
    }

    private function givenFilenameParser_getParsedFilename_returnsPlainFilename(): PlainFilename
    {
        $sourceKey = \Phake::mock(PlainFilename::class);
        \Phake::when($this->filenameParser)->getParsedFilename(\Phake::anyParameters())->thenReturn($sourceKey);
        \Phake::when($sourceKey)->getValue()->thenReturn(self::PARSED_FILENAME_VALUE);

        return $sourceKey;
    }

    private function givenStorageAccessor_getImage_returnsImage(string $publicFilename): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->storageAccessor)->getImage($publicFilename)->thenReturn($image);

        return $image;
    }

    private function assertFilenameParser_getParsedFilename_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->filenameParser, \Phake::times(1))->getParsedFilename($filename);
    }
}
