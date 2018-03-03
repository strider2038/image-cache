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
use Strider2038\ImgCache\Configuration\ConfigurationValidator;

class ConfigurationValidatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider directoryNamesAndValidityProvider
     * @param string $directoryName
     * @param bool $expectedIsValid
     */
    public function isValidDirectoryName_givenDirectoryName_boolReturned(
        string $directoryName,
        bool $expectedIsValid
    ): void {
        $isValid = ConfigurationValidator::isValidDirectoryName($directoryName);

        $this->assertEquals($expectedIsValid, $isValid);
    }

    public function directoryNamesAndValidityProvider(): array
    {
        return [
            ['', 0],
            ['/', 1],
            ['/directory//name/', 0],
            ['/$directory/', 0],
            ['/directory', 1],
            ['directory', 1],
            ['directory/', 1],
            ['/Directory_Name/sub.a-b/', 1],
        ];
    }
}
