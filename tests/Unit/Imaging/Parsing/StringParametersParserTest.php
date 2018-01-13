<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing;

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParser;
use PHPUnit\Framework\TestCase;

class StringParametersParserTest extends TestCase
{
    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Given invalid parameter
     */
    public function parseParameters_givenPatternAndParameterNamesAndInvalidString_invalidRequestValueExceptionThrown(): void
    {
        $parser = new StringParametersParser();

        $parser->parseParameters('/^(?P<x>\d+)$/', new StringList(['x']), 'invalid');
    }

    /**
     * @test
     * @param string $pattern
     * @param array $parameterNames
     * @param string $string
     * @param array $expectedArray
     * @dataProvider patternAndParameterNamesAndValidStringAndValuesProvider
     */
    public function parseParameters_givenPatternAndParameterNamesAndValidString_parameterValuesReturned(
        string $pattern,
        array $parameterNames,
        string $string,
        array $expectedArray
    ): void {
        $parser = new StringParametersParser();

        $parameters = $parser->parseParameters($pattern, new StringList($parameterNames), $string);

        $this->assertArraySubset($expectedArray, $parameters->toArray());
    }

    public function patternAndParameterNamesAndValidStringAndValuesProvider(): array
    {
        return [
            [
                '/^(?P<parameterX>\d+)x(?P<parameterY>\d+)$/',
                ['parameterX', 'parameterY'],
                '20x30',
                [
                    'parameterX' => '20',
                    'parameterY' => '30',
                ]
            ],
            [
                '/^(?P<parameterX>\d+)x(?P<parameterY>\d+)$/',
                ['parameterX'],
                '20x30',
                [
                    'parameterX' => '20',
                ]
            ],
            [
                '/^(?P<parameterX>\d+)(x)?(?P<parameterY>\d+)?$/',
                ['parameterX', 'parameterY'],
                '20',
                [
                    'parameterX' => '20',
                    'parameterY' => '',
                ]
            ],
        ];
    }
}
