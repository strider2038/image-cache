<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\GeoMap;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParameters;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class GeoMapParametersTest extends TestCase
{
    private const GEO_MAP_ID = 'geographical map parameters';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new EntityValidator(
            new CustomConstraintValidatorFactory(
                new MetadataReader()
            ),
            new ViolationFormatter()
        );
    }

    /** @test */
    public function getId_emptyParameters_idReturned(): void
    {
        $mapParameters = new GeoMapParameters();

        $id = $mapParameters->getId();

        $this->assertEquals(self::GEO_MAP_ID, $id);
    }

    /**
     * @test
     * @param string $type
     * @param float $longitude
     * @param float $latitude
     * @param int $zoom
     * @param int $width
     * @param int $height
     * @param float $scale
     * @param string $format
     * @param int $violationsCount
     * @dataProvider geoMapParametersProvider
     */
    public function validate_givenParameters_violationsCountReturned(
        ?string $type,
        ?float $latitude,
        ?float $longitude,
        ?int $zoom,
        ?int $width,
        ?int $height,
        ?float $scale,
        ?string $format,
        int $violationsCount
    ): void {
        $parameters = new GeoMapParameters();
        $parameters->type = $type;
        $parameters->latitude = $latitude;
        $parameters->longitude = $longitude;
        $parameters->zoom = $zoom;
        $parameters->width = $width;
        $parameters->height = $height;
        $parameters->scale = $scale;
        $parameters->format = $format;

        $violations = $this->validator->validate($parameters);

        $this->assertCount($violationsCount, $violations);
    }

    public function geoMapParametersProvider(): array
    {
        return [
            [null, null, null, null, null, null, null, null, 8],
            ['roadmap', 0, 0, 1, 50, 50, 1, 'jpg', 0],
            ['satellite', 0, 0, 1, 50, 50, 1, 'jpg', 0],
            ['hybrid', 0, 0, 1, 50, 50, 1, 'jpg', 0],
            ['terrain', 0, 0, 1, 50, 50, 1, 'jpg', 0],
            ['invalid', 0, 0, 1, 50, 50, 1, 'jpg', 1],
            ['roadmap', -90, 0, 1, 50, 50, 1, 'jpg', 0],
            ['roadmap', -90.1, 0, 1, 50, 50, 1, 'jpg', 1],
            ['roadmap', 90, 0, 1, 50, 50, 1, 'jpg', 0],
            ['roadmap', 90.1, 0, 1, 50, 50, 1, 'jpg', 1],
            ['roadmap', 0, -180, 1, 50, 50, 1, 'jpg', 0],
            ['roadmap', 0, -180.1, 1, 50, 50, 1, 'jpg', 1],
            ['roadmap', 0, 180, 1, 50, 50, 1, 'jpg', 0],
            ['roadmap', 0, 180.1, 1, 50, 50, 1, 'jpg', 1],
            ['roadmap', 0, 0, 1, 50, 50, 1, 'jpg', 0],
            ['roadmap', 0, 0, 0, 50, 50, 1, 'jpg', 1],
            ['roadmap', 0, 0, 20, 50, 50, 1, 'jpg', 0],
            ['roadmap', 0, 0, 21, 50, 50, 1, 'jpg', 1],
            ['roadmap', 0, 0, 1, 50, 50, 1, 'jpg', 0],
            ['roadmap', 0, 0, 1, 49, 50, 1, 'jpg', 1],
            ['roadmap', 0, 0, 1, 640, 50, 1, 'jpg', 0],
            ['roadmap', 0, 0, 1, 641, 50, 1, 'jpg', 1],
            ['roadmap', 0, 0, 1, 50, 50, 1, 'jpg', 0],
            ['roadmap', 0, 0, 1, 50, 49, 1, 'jpg', 1],
            ['roadmap', 0, 0, 1, 50, 640, 1, 'jpg', 0],
            ['roadmap', 0, 0, 1, 50, 641, 1, 'jpg', 1],
            ['roadmap', 0, 0, 1, 50, 50, 1.0, 'jpg', 0],
            ['roadmap', 0, 0, 1, 50, 50, 0.9, 'jpg', 1],
            ['roadmap', 0, 0, 1, 50, 50, 4.0, 'jpg', 0],
            ['roadmap', 0, 0, 1, 50, 50, 4.1, 'jpg', 1],
            ['roadmap', 0, 0, 1, 50, 50, 1, 'jpg', 0],
            ['roadmap', 0, 0, 1, 50, 50, 1, 'jpeg', 0],
            ['roadmap', 0, 0, 1, 50, 50, 1, 'png', 0],
            ['roadmap', 0, 0, 1, 50, 50, 1, 'exe', 1],
        ];
    }
}
