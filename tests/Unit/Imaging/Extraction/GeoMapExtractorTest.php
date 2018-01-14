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

use Strider2038\ImgCache\Imaging\Extraction\GeoMapExtractor;
use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParameters;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParametersParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\GeoMapStorageAccessorInterface;

class GeoMapExtractorTest extends TestCase
{
    private const FILENAME = 'filename';

    /** @var GeoMapParametersParserInterface */
    private $parametersParser;

    /** @var GeoMapStorageAccessorInterface */
    private $storageAccessor;

    protected function setUp(): void
    {
        $this->parametersParser = \Phake::mock(GeoMapParametersParserInterface::class);
        $this->storageAccessor = \Phake::mock(GeoMapStorageAccessorInterface::class);
    }

    /** @test */
    public function getProcessedImage_givenFilename_filenameParsedAndGettedFromStorageAndImageReturned(): void
    {
        $extractor = new GeoMapExtractor($this->parametersParser, $this->storageAccessor);
        $parameters = $this->givenParametersParser_parseMapParametersFromFilename_returnsGeoMapParameters();
        $expectedImage = $this->givenStorageAccessor_getImage_returnsImage();

        $image = $extractor->getProcessedImage(self::FILENAME);

        $this->assertParametersParser_parseMapParametersFromFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertStorageAccessor_getImage_isCalledOnceWithParameters($parameters);
        $this->assertSame($expectedImage, $image);
    }

    private function assertParametersParser_parseMapParametersFromFilename_isCalledOnceWithFilename(
        string $filename
    ): void {
        \Phake::verify($this->parametersParser, \Phake::times(1))->parseMapParametersFromFilename($filename);
    }

    private function assertStorageAccessor_getImage_isCalledOnceWithParameters(GeoMapParameters $parameters): void
    {
        \Phake::verify($this->storageAccessor, \Phake::times(1))->getImage($parameters);
    }

    private function givenParametersParser_parseMapParametersFromFilename_returnsGeoMapParameters(): GeoMapParameters
    {
        $parameters = \Phake::mock(GeoMapParameters::class);
        \Phake::when($this->parametersParser)
            ->parseMapParametersFromFilename(\Phake::anyParameters())
            ->thenReturn($parameters);

        return $parameters;
    }

    private function givenStorageAccessor_getImage_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->storageAccessor)->getImage(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }
}
