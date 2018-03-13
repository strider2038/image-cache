<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Enum\HttpMethodEnum;

class HttpMethodEnumTest extends TestCase
{
    private const OPTIONS = 'OPTIONS';
    private const HEAD = 'HEAD';
    private const GET = 'GET';
    private const POST = 'POST';
    private const PUT = 'PUT';
    private const PATCH = 'PATCH';
    private const DELETE = 'DELETE';

    /**
     * @test
     * @dataProvider HttpMethodAndIsReadMethodProvider
     * @param string $httpMethod
     * @param bool $expectedIsReadMethod
     */
    public function isReadMethod_givenHttpMethod_expectedBoolReturned(
        string $httpMethod,
        bool $expectedIsReadMethod
    ): void {
        $httpMethodEnum = new HttpMethodEnum($httpMethod);

        $isReadMethod = $httpMethodEnum->isReadMethod();

        $this->assertEquals($expectedIsReadMethod, $isReadMethod);
    }

    public function HttpMethodAndIsReadMethodProvider(): array
    {
        return [
            [self::OPTIONS, true],
            [self::HEAD, true],
            [self::GET, true],
            [self::POST, false],
            [self::PUT, false],
            [self::PATCH, false],
            [self::DELETE, false],
        ];
    }
}
