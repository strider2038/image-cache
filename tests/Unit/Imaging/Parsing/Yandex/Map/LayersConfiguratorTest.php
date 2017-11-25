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
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;

class LayersConfiguratorTest extends TestCase
{
    /** @test */
    public function configure_givenValue_valueIsParsedAndSetToParameters(): void
    {
        $configurator = new LayersConfigurator();
        $parameters = new YandexMapParameters();

        $configurator->configure('val1,val2,val3', $parameters);

        $this->assertCount(3, $parameters->getLayers());
        $this->assertContains('val3', $parameters->getLayers()->toArray());
    }
}
