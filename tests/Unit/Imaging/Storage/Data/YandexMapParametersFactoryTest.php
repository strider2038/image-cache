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
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParametersFactory;

class YandexMapParametersFactoryTest extends TestCase
{
    private const DEFAULT_LAYERS = ['map'];
    private const DEFAULT_LONGITUDE = 0;
    private const DEFAULT_LATITUDE = 0;
    private const DEFAULT_ZOOM = 0;
    private const DEFAULT_WIDTH = 450;
    private const DEFAULT_HEIGHT = 300;
    private const DEFAULT_SCALE = 1.0;
    private const LAYERS = ['skl'];
    private const LONGITUDE = 10;
    private const LATITUDE = 15;
    private const ZOOM = 2;
    private const WIDTH = 45;
    private const HEIGHT = 30;
    private const SCALE = 2.5;

    /** @test */
    public function create_givenDefaultParameters_objectWithDefaultParametersIsReturned(): void
    {
        $factory = new YandexMapParametersFactory();

        $parameters = $factory->create();

        $this->assertEquals(self::DEFAULT_LAYERS, $parameters->getLayers()->toArray());
        $this->assertEquals(self::DEFAULT_LONGITUDE, $parameters->getLongitude());
        $this->assertEquals(self::DEFAULT_LATITUDE, $parameters->getLatitude());
        $this->assertEquals(self::DEFAULT_ZOOM, $parameters->getZoom());
        $this->assertEquals(self::DEFAULT_WIDTH, $parameters->getWidth());
        $this->assertEquals(self::DEFAULT_HEIGHT, $parameters->getHeight());
        $this->assertEquals(self::DEFAULT_SCALE, $parameters->getScale());
    }

    /** @test */
    public function create_givenParameters_objectWithGivenParametersIsReturned(): void
    {
        $factory = new YandexMapParametersFactory();
        $factory->setLayers(new StringList(self::LAYERS));
        $factory->setLongitude(self::LONGITUDE);
        $factory->setLatitude(self::LATITUDE);
        $factory->setZoom(self::ZOOM);
        $factory->setWidth(self::WIDTH);
        $factory->setHeight(self::HEIGHT);
        $factory->setScale(self::SCALE);

        $parameters = $factory->create();

        $this->assertEquals(self::LAYERS, $parameters->getLayers()->toArray());
        $this->assertEquals(self::LONGITUDE, $parameters->getLongitude());
        $this->assertEquals(self::LATITUDE, $parameters->getLatitude());
        $this->assertEquals(self::ZOOM, $parameters->getZoom());
        $this->assertEquals(self::WIDTH, $parameters->getWidth());
        $this->assertEquals(self::HEIGHT, $parameters->getHeight());
        $this->assertEquals(self::SCALE, $parameters->getScale());
    }
}
