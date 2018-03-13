<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional\Services;

use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParametersParserInterface;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GeoMapParametersParserTest extends FunctionalTestCase
{
    /** @var GeoMapParametersParserInterface */
    private $geoMapParametersParser;

    protected function setUp(): void
    {
        $container = $this->loadContainer('services/geo-map-parameters-parser.yml');
        $this->geoMapParametersParser = $container->get('geo_map_parameters_parser');
    }

    /**
     * @test
     * @param string $filename
     * @param null|string $type
     * @param float|null $latitude
     * @param float|null $longitude
     * @param int|null $zoom
     * @param int|null $width
     * @param int|null $height
     * @param float|null $scale
     * @param null|string $format
     * @dataProvider filenameAndGeoMapParametersProvider
     */
    public function parseMapParametersFromFilename_givenFilename_geoMapParametersCreatedAndReturned(
        string $filename,
        ?string $type,
        ?float $latitude,
        ?float $longitude,
        ?int $zoom,
        ?int $width,
        ?int $height,
        ?float $scale,
        ?string $format
    ): void {
        $parameters = $this->geoMapParametersParser->parseMapParametersFromFilename($filename);

        $this->assertEquals($type, $parameters->type);
        $this->assertEquals($latitude, $parameters->latitude);
        $this->assertEquals($longitude, $parameters->longitude);
        $this->assertEquals($zoom, $parameters->zoom);
        $this->assertEquals($width, $parameters->width);
        $this->assertEquals($height, $parameters->height);
        $this->assertEquals($scale, $parameters->scale);
        $this->assertEquals($format, $parameters->imageFormat);
    }

    public function filenameAndGeoMapParametersProvider(): array
    {
        return [
            ['center60,40.jpg', 'roadmap', 60, 40, 14, 600, 450, 1.0, 'jpg'],
            ['center-20.53,-10.91.jpg', 'roadmap', -20.53, -10.91, 14, 600, 450, 1.0, 'jpg'],
            ['c+6,+1.jpg', 'roadmap', 6, 1, 14, 600, 450, 1.0, 'jpg'],
            ['c0,0_roadmap.jpg', 'roadmap', 0, 0, 14, 600, 450, 1.0, 'jpg'],
            ['c0,0_satellite.jpg', 'satellite', 0, 0, 14, 600, 450, 1.0, 'jpg'],
            ['c0,0_hybrid.jpg', 'hybrid', 0, 0, 14, 600, 450, 1.0, 'jpg'],
            ['c0,0_terrain.jpg', 'terrain', 0, 0, 14, 600, 450, 1.0, 'jpg'],
            ['c0,0_zoom20.jpg', 'roadmap', 0, 0, 20, 600, 450, 1.0, 'jpg'],
            ['c0,0_z1.jpg', 'roadmap', 0, 0, 1, 600, 450, 1.0, 'jpg'],
            ['c0,0_size100x50.jpg', 'roadmap', 0, 0, 14, 100, 50, 1.0, 'jpg'],
            ['c0,0_s150x200.jpg', 'roadmap', 0, 0, 14, 150, 200, 1.0, 'jpg'],
            ['c0,0_scale2.jpg', 'roadmap', 0, 0, 14, 600, 450, 2.0, 'jpg'],
            ['c0,0_scale3.5.jpg', 'roadmap', 0, 0, 14, 600, 450, 3.5, 'jpg'],
            ['c0,0_sc1.5.jpg', 'roadmap', 0, 0, 14, 600, 450, 1.5, 'jpg'],
        ];
    }
}
