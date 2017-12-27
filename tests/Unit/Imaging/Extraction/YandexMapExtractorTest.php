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
use Strider2038\ImgCache\Imaging\Extraction\YandexMapExtractor;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\YandexMapParametersParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\YandexMapStorageAccessorInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;

class YandexMapExtractorTest extends TestCase
{
    private const KEY = 'key';

    /** @var YandexMapParametersParserInterface */
    private $parser;

    /** @var YandexMapStorageAccessorInterface */
    private $storageAccessor;

    protected function setUp(): void
    {
        $this->parser = \Phake::mock(YandexMapParametersParserInterface::class);
        $this->storageAccessor = \Phake::mock(YandexMapStorageAccessorInterface::class);
    }

    /** @test */
    public function getProcessedImage_givenKey_keyIsParsedAndImageIsReturned(): void
    {
        $extractor = new YandexMapExtractor($this->parser, $this->storageAccessor);
        $parameters = $this->givenParser_parse_returnsParameters();
        $expectedImage = $this->givenStorageAccessor_getImage_returnsImage($parameters);

        $image = $extractor->getProcessedImage(self::KEY);

        $this->assertSame($expectedImage, $image);
    }

    private function givenParser_parse_returnsParameters(): YandexMapParameters
    {
        $parameters = new YandexMapParameters();
        \Phake::when($this->parser)->parse(self::KEY)->thenReturn($parameters);

        return $parameters;
    }

    private function givenStorageAccessor_getImage_returnsImage(YandexMapParameters $parameters): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->storageAccessor)->getImage($parameters)->thenReturn($image);

        return $image;
    }
}
