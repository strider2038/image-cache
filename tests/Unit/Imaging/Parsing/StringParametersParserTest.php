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

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParser;

class StringParametersParserTest extends TestCase
{
    /** @test */
    public function parseParameters_givenPatternAndParameterNamesAndInvalidString_invalidRequestValueExceptionThrown(): void
    {
        $parser = new StringParametersParser();

        $parameters = $parser->parseParameters('/^(?P<x>\d+)$/', 'invalid');

        $this->assertCount(0, $parameters);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Given invalid parameter
     */
    public function strictlyParseParameters_givenPatternAndParameterNamesAndInvalidString_invalidRequestValueExceptionThrown(): void
    {
        $parser = new StringParametersParser();

        $parser->strictlyParseParameters('/^(?P<x>\d+)$/', 'invalid');
    }

    /**
     * @test
     * @param string $pattern
     * @param string $string
     * @param array $expectedArray
     * @dataProvider patternAndParameterNamesAndValidStringAndValuesProvider
     */
    public function strictlyParseParameters_givenPatternAndParameterNamesAndValidString_parameterValuesReturned(
        string $pattern,
        string $string,
        array $expectedArray
    ): void {
        $parser = new StringParametersParser();

        $parameters = $parser->strictlyParseParameters($pattern, $string);

        $this->assertArraySubset($expectedArray, $parameters->toArray());
    }

    public function patternAndParameterNamesAndValidStringAndValuesProvider(): array
    {
        return [
            [
                '/^(?P<parameterX>\d+)x(?P<parameterY>\d+)$/',
                '20x30',
                [
                    'parameterX' => '20',
                    'parameterY' => '30',
                ]
            ],
            [
                '/^(?P<parameterX>\d+)x(?P<parameterY>\d+)$/',
                '20x30',
                [
                    'parameterX' => '20',
                ]
            ],
            [
                '/^(?P<parameterX>\d+)(x)?(?P<parameterY>\d+)?$/',
                '20',
                [
                    'parameterX' => '20',
                    'parameterY' => '',
                ]
            ],
        ];
    }
}
