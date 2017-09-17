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
use Strider2038\ImgCache\Core\Uri;

class UriTest extends TestCase
{
    const URI_VALUE = 'http://username:password@hostname:9090/path?arg=value#anchor';

    /**
     * @test
     * @param string $method
     * @param string $expectedResult
     * @dataProvider methodNameAndReturnedResultProvider
     */
    public function givenMethod_givenUriValue_expectedResultIsReturned(
        string $method,
        string $expectedResult
    ): void {
        $uri = new Uri(self::URI_VALUE);

        $result = $uri->$method();

        $this->assertEquals($expectedResult, $result);
    }

    public function methodNameAndReturnedResultProvider(): array
    {
        return [
            ['getScheme', 'http'],
            ['getAuthority', 'username:password@hostname:9090'],
            ['getUserInfo', 'username:password'],
            ['getHost', 'hostname'],
            ['getPort', 9090],
            ['getPath', '/path'],
            ['getQuery', 'arg=value'],
            ['getFragment', 'anchor'],
        ];
    }

    public function testToString_GivenValue_ReturnedValue(): void
    {
        $uri = new Uri(self::URI_VALUE);

        $result = $uri . '';

        $this->assertEquals(self::URI_VALUE, $result);
    }
}
