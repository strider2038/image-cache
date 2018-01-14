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
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParametersFactory;

class GeoMapParametersFactoryTest extends TestCase
{
    /** @test */
    public function createGeoMapParameters_givenDefaultParameterValues_geoMapParametersWithDefaultValuesCreatedAndReturned(): void
    {
        $factory = new GeoMapParametersFactory();

        $parameters = $factory->createGeoMapParameters();

        $this->assertInstanceOf(GeoMapParameters::class, $parameters);
        $this->assertEquals('roadmap', $parameters->type);
        $this->assertNull($parameters->latitude);
        $this->assertNull($parameters->longitude);
        $this->assertEquals(14, $parameters->zoom);
        $this->assertEquals(600, $parameters->width);
        $this->assertEquals(450, $parameters->height);
        $this->assertEquals(1.0, $parameters->scale);
        $this->assertNull($parameters->format);
    }

    /** @test */
    public function createGeoMapParameters_givenCustomParameterValues_geoMapParametersWithDefaultValuesCreatedAndReturned(): void
    {
        $factory = new GeoMapParametersFactory();
        $factory->setDefaultType('satellite');
        $factory->setDefaultLatitude(60);
        $factory->setDefaultLongitude(40);
        $factory->setDefaultZoom(10);
        $factory->setDefaultWidth(300);
        $factory->setDefaultHeight(250);
        $factory->setDefaultScale(2.5);

        $parameters = $factory->createGeoMapParameters();

        $this->assertInstanceOf(GeoMapParameters::class, $parameters);
        $this->assertEquals('satellite', $parameters->type);
        $this->assertEquals(60, $parameters->latitude);
        $this->assertEquals(40, $parameters->longitude);
        $this->assertEquals(10, $parameters->zoom);
        $this->assertEquals(300, $parameters->width);
        $this->assertEquals(250, $parameters->height);
        $this->assertEquals(2.5, $parameters->scale);
    }
}
