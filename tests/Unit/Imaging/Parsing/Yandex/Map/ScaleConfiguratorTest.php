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
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\ScaleConfigurator;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParameters;

class ScaleConfiguratorTest extends TestCase
{
    /** @test */
    public function configure_givenValue_valueIsParsedAndSetToParameters(): void
    {
        $configurator = new ScaleConfigurator();
        $parameters = new YandexMapParameters();

        $configurator->configure('3.0', $parameters);

        $this->assertEquals(3.0, $parameters->getScale());
    }
}
