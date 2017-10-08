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
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\YandexMapParametersParserInterface;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParametersInterface;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapSourceInterface;

class YandexMapExtractorTest extends TestCase
{
    private const KEY = 'key';

    /** @var YandexMapParametersParserInterface */
    private $parser;

    /** @var YandexMapSourceInterface */
    private $source;

    protected function setUp()
    {
        $this->parser = \Phake::mock(YandexMapParametersParserInterface::class);
        $this->source = \Phake::mock(YandexMapSourceInterface::class);
    }

    /** @test */
    public function extract_givenKey_keyIsParsedAndImageIsReturned(): void
    {
        $extractor = new YandexMapExtractor($this->parser, $this->source);
        $parameters = $this->givenParser_parse_returnsParameters();
        $this->givenSource_get_returnsImage($parameters);

        $image = $extractor->extract(self::KEY);

        $this->assertInstanceOf(ImageInterface::class, $image);

    }

    private function givenParser_parse_returnsParameters(): YandexMapParametersInterface
    {
        $parameters = \Phake::mock(YandexMapParametersInterface::class);
        \Phake::when($this->parser)->parse(self::KEY)->thenReturn($parameters);

        return $parameters;
    }

    private function givenSource_get_returnsImage(YandexMapParametersInterface $parameters): void
    {
        $expectedImage = \Phake::mock(ImageInterface::class);
        \Phake::when($this->source)->get($parameters)->thenReturn($expectedImage);
    }
}
