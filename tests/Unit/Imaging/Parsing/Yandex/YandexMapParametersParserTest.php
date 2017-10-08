<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Yandex;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\ValueConfiguratorFactoryInterface;

class YandexMapParametersParserTest extends TestCase
{
    /** @var ValueConfiguratorFactoryInterface */
    private $valueConfiguratorFactory;

    protected function setUp()
    {
        $this->valueConfiguratorFactory = \Phake::mock(ValueConfiguratorFactoryInterface::class);
    }

    /** @test */
    public function parse_givenKey_keysAndValuesAreParsedAnParametersAreReturned(): void
    {

    }
}
