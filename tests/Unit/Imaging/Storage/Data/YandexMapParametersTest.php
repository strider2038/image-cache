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
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class YandexMapParametersTest extends TestCase
{
    private const LAYERS = ['sat'];
    private const LONGITUDE = 10;
    private const LATITUDE = 20;
    private const ZOOM = 15;
    private const WIDTH = 120;
    private const HEIGHT = 90;
    private const SCALE = 1.5;
    private const JSON_ARRAY = [
        'layers' => self::LAYERS,
        'longitude' => self::LONGITUDE,
        'latitude' => self::LATITUDE,
        'zoom' => self::ZOOM,
        'width' => self::WIDTH,
        'height' => self::HEIGHT,
        'scale' => self::SCALE,
    ];
    private const YANDEX_MAP_PARAMETERS_ID = 'yandex map parameters';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new EntityValidator(new ViolationFormatter());
    }

    /** @test */
    public function getId_emptyParameters_idReturned(): void
    {
        $parameters = new YandexMapParameters();

        $id = $parameters->getId();

        $this->assertEquals(self::YANDEX_MAP_PARAMETERS_ID, $id);
    }

    /**
     * @test
     * @dataProvider parametersAndViolationsCountProvider
     * @param array $layers
     * @param float $longitude
     * @param float $latitude
     * @param int $zoom
     * @param int $width
     * @param int $height
     * @param float $scale
     * @param int $violationsCount
     */
    public function validate_givenParameters_expectedViolationsCountFound(
        array $layers,
        float $longitude,
        float $latitude,
        int $zoom,
        int $width,
        int $height,
        float $scale,
        int $violationsCount
    ): void {
        $model = new YandexMapParameters();
        $model->setLayers(new StringList($layers));
        $model->setLongitude($longitude);
        $model->setLatitude($latitude);
        $model->setZoom($zoom);
        $model->setWidth($width);
        $model->setHeight($height);
        $model->setScale($scale);

        $violations = $this->validator->validate($model);

        $this->assertEquals($violationsCount, $violations->count());
    }

    public function parametersAndViolationsCountProvider(): array
    {
        return [
            [self::LAYERS, 0, 0, 0, 100, 150, 1.0, 0],
            [['map'], 0, 0, 0, 100, 150, 1.0, 0],
            [self::LAYERS, 0, 0, 0, 100, 150, 1.0, 0],
            [['skl'], 0, 0, 0, 100, 150, 1.0, 0],
            [['trf'], 0, 0, 0, 100, 150, 1.0, 0],
            [['map', 'trf'], 0, 0, 0, 100, 150, 1.0, 0],
            [['sat', 'skl'], 0, 0, 0, 100, 150, 1.0, 0],
            [['trf', 'sat', 'skl'], 0, 0, 0, 100, 150, 1.0, 0],
            [[], 0, 0, 0, 100, 150, 1.0, 1],
            [['a'], 0, 0, 0, 100, 150, 1.0, 1],
            [['a', 'b'], 0, 0, 0, 100, 150, 1.0, 2],
            [self::LAYERS, -180.1, 0, 1, 100, 150, 1.0, 1],
            [self::LAYERS, 180.1, 0, 1, 100, 150, 1.0, 1],
            [self::LAYERS, -180, 180.1, 1, 100, 150, 1.0, 1],
            [self::LAYERS, 180, -180.1, 1, 100, 150, 1.0, 1],
            [self::LAYERS, 0, 0, -1, 100, 150, 1.0, 1],
            [self::LAYERS, 0, 0, 18, 100, 150, 1.0, 1],
            [self::LAYERS, 0, 0, 0, 100, 150, 1.0, 0],
            [self::LAYERS, 0, 0, 17, 100, 150, 1.0, 0],
            [self::LAYERS, 0, 0, 0, 49, 150, 1.0, 1],
            [self::LAYERS, 0, 0, 0, 50, 150, 1.0, 0],
            [self::LAYERS, 0, 0, 0, 650, 150, 1.0, 0],
            [self::LAYERS, 0, 0, 0, 651, 150, 1.0, 1],
            [self::LAYERS, 0, 0, 0, 100, 49, 1.0, 1],
            [self::LAYERS, 0, 0, 0, 100, 50, 1.0, 0],
            [self::LAYERS, 0, 0, 0, 100, 450, 1.0, 0],
            [self::LAYERS, 0, 0, 0, 100, 451, 1.0, 1],
            [self::LAYERS, 0, 0, 0, 100, 150, 0.9, 1],
            [self::LAYERS, 0, 0, 0, 100, 150, 1.0, 0],
            [self::LAYERS, 0, 0, 0, 100, 150, 4.0, 0],
            [self::LAYERS, 0, 0, 0, 100, 150, 4.1, 1],
            [self::LAYERS, 0, 0, 0, 100, 150, 1.0, 0],
        ];
    }

    /** @test */
    public function jsonSerialize_givenParameters_validJsonIsReturned(): void
    {
        $model = new YandexMapParameters();
        $model->setLayers(new StringList(self::LAYERS));
        $model->setLongitude(self::LONGITUDE);
        $model->setLatitude(self::LATITUDE);
        $model->setZoom(self::ZOOM);
        $model->setWidth(self::WIDTH);
        $model->setHeight(self::HEIGHT);
        $model->setScale(self::SCALE);

        $json = json_encode($model);

        $this->assertEquals(json_encode(self::JSON_ARRAY), $json);
    }
}
