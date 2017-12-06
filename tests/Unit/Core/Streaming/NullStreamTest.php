<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core\Streaming;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Streaming\NullStream;

class NullStreamTest extends TestCase
{
    /**
     * @test
     * @param string $method
     * @param $expectedResult
     * @dataProvider methodAndResultProvider
     */
    public function givenMethod_givenParameters_expectedResult(string $method, $expectedResult): void {
        $stream = new NullStream();

        $result = $stream->$method();

        $this->assertEquals($expectedResult, $result);
    }

    public function methodAndResultProvider(): array
    {
        return [
            ['getContents', ''],
            ['close', null],
            ['getSize', null],
            ['eof', true],
            ['isWritable', false],
            ['isReadable', false],
            ['rewind', null],
        ];
    }
    /**
     * @test
     * @param string $method
     * @param string $parameter
     * @dataProvider methodAndParametersProvider
     * @expectedException \RuntimeException
     */
    public function givenMethod_givenParameters_exceptionThrown(string $method, string $parameter): void {
        $stream = new NullStream();

        call_user_func([$stream, $method], $parameter);
    }

    public function methodAndParametersProvider(): array
    {
        return [
            ['write', ''],
            ['read', 1],
        ];
    }

    /** @test */
    public function toString_nop_emptyStringIsReturned(): void {
        $stream = new NullStream();

        $result = $stream . '';

        $this->assertEquals('', $result);
    }
}
