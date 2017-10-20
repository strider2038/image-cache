<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Source\Yandex;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Validation\ModelValidator;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;

class YandexMapParametersTest extends TestCase
{
    /** @var ModelValidatorInterface */
    private $validator;

    protected function setUp()
    {
        $this->validator = new ModelValidator();
    }

    /**
     * @test
     * @dataProvider parametersAndViolationsCountProvider
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
            [['sat'], 0, 0, 0, 100, 150, 1.0, 0],
            [['map'], 0, 0, 0, 100, 150, 1.0, 0],
            [['sat'], 0, 0, 0, 100, 150, 1.0, 0],
            [['skl'], 0, 0, 0, 100, 150, 1.0, 0],
            [['trf'], 0, 0, 0, 100, 150, 1.0, 0],
            [['map', 'trf'], 0, 0, 0, 100, 150, 1.0, 0],
            [['sat', 'skl'], 0, 0, 0, 100, 150, 1.0, 0],
            [['trf', 'sat', 'skl'], 0, 0, 0, 100, 150, 1.0, 0],
            [['a'], 0, 0, 0, 100, 150, 1.0, 1],
            [['a', 'b'], 0, 0, 0, 100, 150, 1.0, 2],
            [['sat'], -180.1, 0, 1, 100, 150, 1.0, 1],
            [['sat'], 180.1, 0, 1, 100, 150, 1.0, 1],
            [['sat'], -180, 180.1, 1, 100, 150, 1.0, 1],
            [['sat'], 180, -180.1, 1, 100, 150, 1.0, 1],
            [['sat'], 0, 0, -1, 100, 150, 1.0, 1],
            [['sat'], 0, 0, 18, 100, 150, 1.0, 1],
            [['sat'], 0, 0, 0, 100, 150, 1.0, 0],
            [['sat'], 0, 0, 17, 100, 150, 1.0, 0],
            [['sat'], 0, 0, 0, 49, 150, 1.0, 1],
            [['sat'], 0, 0, 0, 50, 150, 1.0, 0],
            [['sat'], 0, 0, 0, 650, 150, 1.0, 0],
            [['sat'], 0, 0, 0, 651, 150, 1.0, 1],
            [['sat'], 0, 0, 0, 100, 49, 1.0, 1],
            [['sat'], 0, 0, 0, 100, 50, 1.0, 0],
            [['sat'], 0, 0, 0, 100, 450, 1.0, 0],
            [['sat'], 0, 0, 0, 100, 451, 1.0, 1],
            [['sat'], 0, 0, 0, 100, 150, 0.9, 1],
            [['sat'], 0, 0, 0, 100, 150, 1.0, 0],
            [['sat'], 0, 0, 0, 100, 150, 4.0, 0],
            [['sat'], 0, 0, 0, 100, 150, 4.1, 1],
            [['sat'], 0, 0, 0, 100, 150, 1.0, 0],
        ];
    }
}
