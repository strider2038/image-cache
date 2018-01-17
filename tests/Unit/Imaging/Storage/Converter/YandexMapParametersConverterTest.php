<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Converter;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Converter\YandexMapParametersConverter;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParametersFactoryInterface;

class YandexMapParametersConverterTest extends TestCase
{
    private const LAYERS = ['map', 'sat'];
    private const LATITUDE = 60.25;
    private const LONGITUDE = -40.123;
    private const ZOOM = 9;
    private const WIDTH = 400;
    private const HEIGHT = 300;
    private const SCALE = 2.5;
    private const QUERY_LAYERS = ['l' => 'map,sat'];
    private const QUERY_LONGITUDE_AND_LATITUDE = ['ll' => '-40.123,60.25'];
    private const QUERY_ZOOM = ['z' => self::ZOOM];
    private const QUERY_SIZE = ['size' => '400,300'];
    private const QUERY_SCALE = ['scale' => self::SCALE];

    /** @var YandexMapParametersFactoryInterface */
    private $yandexMapParametersFactory;

    protected function setUp(): void
    {
        $this->yandexMapParametersFactory = \Phake::mock(YandexMapParametersFactoryInterface::class);
    }

    /** @test */
    public function convertGeoMapParametersToQuery_givenGeoMapParameters_queryParameterCollectionCreatedAndReturned(): void
    {
        $converter = new YandexMapParametersConverter($this->yandexMapParametersFactory);
        $geoMapParameters = \Phake::mock(GeoMapParameters::class);
        $yandexMapParameters = new YandexMapParameters();
        $yandexMapParameters->layers = new StringList(self::LAYERS);
        $yandexMapParameters->latitude = self::LATITUDE;
        $yandexMapParameters->longitude = self::LONGITUDE;
        $yandexMapParameters->zoom = self::ZOOM;
        $yandexMapParameters->width = self::WIDTH;
        $yandexMapParameters->height = self::HEIGHT;
        $yandexMapParameters->scale = self::SCALE;
        $this->givenYandexMapParametersFactory_createYandexMapParametersFromGeoMapParameters_returnsYandexMapParameters($yandexMapParameters);

        $query = $converter->convertGeoMapParametersToQuery($geoMapParameters);

        $this->assertInstanceOf(QueryParameterCollection::class, $query);
        $this->assertYandexMapParametersFactory_createYandexMapParametersFromGeoMapParameters_isCalledOnceWithGeoMapParameters($geoMapParameters);
        $this->assertCount(5, $query);
        $queryParameters = $query->toArray();
        $this->assertArraySubset(self::QUERY_LAYERS, $queryParameters);
        $this->assertArraySubset(self::QUERY_LONGITUDE_AND_LATITUDE, $queryParameters);
        $this->assertArraySubset(self::QUERY_ZOOM, $queryParameters);
        $this->assertArraySubset(self::QUERY_SIZE, $queryParameters);
        $this->assertArraySubset(self::QUERY_SCALE, $queryParameters);
    }

    private function givenYandexMapParametersFactory_createYandexMapParametersFromGeoMapParameters_returnsYandexMapParameters($yandexMapParameters): void
    {
        \Phake::when($this->yandexMapParametersFactory)
            ->createYandexMapParametersFromGeoMapParameters(\Phake::anyParameters())
            ->thenReturn($yandexMapParameters);
    }

    private function assertYandexMapParametersFactory_createYandexMapParametersFromGeoMapParameters_isCalledOnceWithGeoMapParameters(
        GeoMapParameters $geoMapParameters
    ): void {
        \Phake::verify($this->yandexMapParametersFactory, \Phake::times(1))
            ->createYandexMapParametersFromGeoMapParameters($geoMapParameters);
    }
}
