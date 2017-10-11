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
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\LayersConfigurator;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\LongitudeAndLatitudeConfigurator;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\ScaleConfigurator;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\ValueConfiguratorFactory;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\WidthAndHeightConfigurator;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\ZoomConfigurator;

class ValueConfiguratorFactoryTest extends TestCase
{
    /**
     * @test
     * @param string $name
     * @param string $class
     * @dataProvider nameAndClassProvider
     */
    public function create_givenName_configuratorClassIsCreated(string $name, string $class): void
    {
        $factory = new ValueConfiguratorFactory();

        $configurator = $factory->create($name);

        $this->assertInstanceOf($class, $configurator);
    }

    public function nameAndClassProvider(): array
    {
        return [
            ['l', LayersConfigurator::class],
            ['ll', LongitudeAndLatitudeConfigurator::class],
            ['z', ZoomConfigurator::class],
            ['size', WidthAndHeightConfigurator::class],
            ['scale', ScaleConfigurator::class],
        ];
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Unknown parameter name
     */
    public function create_givenInvalidName_exceptionThrown(): void
    {
        $factory = new ValueConfiguratorFactory();

        $factory->create('');
    }
}
