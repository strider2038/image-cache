<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Data;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParametersFactory;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class YandexMapParametersFactoryTest extends TestCase
{
    private const LAYERS = ['skl'];
    private const LONGITUDE = 10;
    private const LATITUDE = 15;
    private const ZOOM = 2;
    private const WIDTH = 45;
    private const HEIGHT = 30;
    private const SCALE = 2.5;
    private const TYPE_ROADMAP = 'roadmap';
    private const LAYERS_FOR_ROADMAP = ['map'];
    private const TYPE_SATELLITE = 'satellite';
    private const LAYERS_FOR_SATELLITE = ['sat'];
    private const TYPE_HYBRID = 'hybrid';
    private const LAYERS_FOR_HYBRID = ['map', 'sat'];

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
    }

    /** @test */
    public function createYandexMapParametersFromGeoMapParameters_givenGeoMapParameters_validYandexMapParametersCreatedAndReturned(): void
    {
        $factory = $this->createYandexMapParametersFactory();
        $geoMapParameters = new GeoMapParameters();
        $geoMapParameters->type = self::TYPE_ROADMAP;
        $geoMapParameters->latitude = self::LATITUDE;
        $geoMapParameters->longitude = self::LONGITUDE;
        $geoMapParameters->zoom = self::ZOOM;
        $geoMapParameters->width = self::WIDTH;
        $geoMapParameters->height = self::HEIGHT;
        $geoMapParameters->scale = self::SCALE;

        $yandexMapParameters = $factory->createYandexMapParametersFromGeoMapParameters($geoMapParameters);

        $this->assertEquals(self::LAYERS_FOR_ROADMAP, $yandexMapParameters->layers->toArray());
        $this->assertEquals(self::LATITUDE, $yandexMapParameters->latitude);
        $this->assertEquals(self::LONGITUDE, $yandexMapParameters->longitude);
        $this->assertEquals(self::ZOOM, $yandexMapParameters->zoom);
        $this->assertEquals(self::WIDTH, $yandexMapParameters->width);
        $this->assertEquals(self::HEIGHT, $yandexMapParameters->height);
        $this->assertEquals(self::SCALE, $yandexMapParameters->scale);
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            YandexMapParameters::class,
            InvalidRequestValueException::class
        );
    }

    /**
     * @test
     * @param string $mapType
     * @param array $layers
     * @dataProvider mapTypeAndLayersProvider
     */
    public function createYandexMapParametersFromGeoMapParameters_givenMapType_mapTypeProperlyConvertedToLayers(
        string $mapType,
        array $layers
    ): void {
        $factory = $this->createYandexMapParametersFactory();
        $geoMapParameters = new GeoMapParameters();
        $geoMapParameters->type = $mapType;

        $yandexMapParameters = $factory->createYandexMapParametersFromGeoMapParameters($geoMapParameters);

        $this->assertEquals($layers, $yandexMapParameters->layers->toArray());
    }

    public function mapTypeAndLayersProvider(): array
    {
        return [
            ['', []],
            [self::TYPE_ROADMAP, self::LAYERS_FOR_ROADMAP],
            [self::TYPE_SATELLITE, self::LAYERS_FOR_SATELLITE],
            [self::TYPE_HYBRID, self::LAYERS_FOR_HYBRID],
        ];
    }

    private function createYandexMapParametersFactory(): YandexMapParametersFactory
    {
        return new YandexMapParametersFactory($this->validator);
    }

    private function assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
        string $entityClass,
        string $exceptionClass
    ): void {
        \Phake::verify($this->validator, \Phake::times(1))
            ->validateWithException(\Phake::capture($entity), \Phake::capture($exception));
        $this->assertInstanceOf($entityClass, $entity);
        $this->assertEquals($exceptionClass, $exception);
    }
}
