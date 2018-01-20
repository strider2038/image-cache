<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationFactory;

class ConfigurationFactoryTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /** @test */
    public function createConfiguration_givenConfigurationArray_configurationClassCreatedAndReturned(): void
    {
        $factory = new ConfigurationFactory();

        $configuration = $factory->createConfiguration([]);

        $this->assertInstanceOf(Configuration::class, $configuration);
    }
}
