<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Yandex\Map;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\LongitudeAndLatitudeConfigurator;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParameters;

class LongitudeAndLatitudeConfiguratorTest extends TestCase
{
    private const LONGITUDE = 37.620070;
    private const LATITUDE = 55.753630;
    private const VALUE = self::LONGITUDE . ',' . self::LATITUDE;

    /** @test */
    public function configure_givenValue_valueIsParsedAndSetToParameters(): void
    {
        $configurator = new LongitudeAndLatitudeConfigurator();
        $parameters = new YandexMapParameters();

        $configurator->configure(self::VALUE, $parameters);

        $this->assertEquals(self::LONGITUDE, $parameters->getLongitude());
        $this->assertEquals(self::LATITUDE, $parameters->getLatitude());
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Number of values is incorrect
     */
    public function configure_givenInvalidValue_exceptionThrown(): void
    {
        $configurator = new LongitudeAndLatitudeConfigurator();
        $parameters = new YandexMapParameters();

        $configurator->configure('', $parameters);
    }
}
