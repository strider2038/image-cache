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
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\WidthAndHeightConfigurator;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParameters;

class WidthAndHeightConfiguratorTest extends TestCase
{
    private const WIDTH = 100;
    private const HEIGHT = 200;
    private const VALUE = self::WIDTH . ',' . self::HEIGHT;

    /** @test */
    public function configure_givenValue_valueIsParsedAndSetToParameters(): void
    {
        $configurator = new WidthAndHeightConfigurator();
        $parameters = new YandexMapParameters();

        $configurator->configure(self::VALUE, $parameters);

        $this->assertEquals(self::WIDTH, $parameters->getWidth());
        $this->assertEquals(self::HEIGHT, $parameters->getHeight());
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Number of values is incorrect
     */
    public function configure_givenInvalidValue_exceptionThrown(): void
    {
        $configurator = new WidthAndHeightConfigurator();
        $parameters = new YandexMapParameters();

        $configurator->configure('', $parameters);
    }
}
