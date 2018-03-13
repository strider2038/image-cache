<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\ApplicationParameters;

class ApplicationParametersTest extends TestCase
{
    private const ROOT_DIRECTORY = 'root_directory';
    private const SERVER_CONFIGURATION = ['server_configuration'];

    /** @test */
    public function construct_givenRootDirectoryAndServerConfiguration_parametersSet(): void
    {
        $parameters = new ApplicationParameters(
            self::ROOT_DIRECTORY,
            self::SERVER_CONFIGURATION
        );

        $this->assertEquals(self::ROOT_DIRECTORY, $parameters->getRootDirectory());
        $this->assertEquals(self::SERVER_CONFIGURATION, $parameters->getServerConfiguration());
        $this->assertGreaterThan(0, $parameters->getStartUpTime());
    }
}
