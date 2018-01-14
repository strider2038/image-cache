<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Transforming;

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParserInterface;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ShiftingTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ShiftingTransformationFactory;
use PHPUnit\Framework\TestCase;

class ShiftingTransformationFactoryTest extends TestCase
{
    private const STRING_PARAMETERS = 'String Parameters';
    private const STRING_PARAMETERS_IN_LOWER_CASE = 'string parameters';
    private const PARSING_PATTERN = '/^(x(?P<x>-?\d*))?(y(?P<y>-?\d*))?$/';

    /** @var StringParametersParserInterface */
    private $parametersParser;

    protected function setUp(): void
    {
        $this->parametersParser = \Phake::mock(StringParametersParserInterface::class);
    }

    /**
     * @test
     * @param string $stringX
     * @param string $stringY
     * @param int $shiftPointX
     * @param int $shiftPointY
     * @dataProvider shiftParametersProvider
     */
    public function createTransformation_givenStringParameters_shiftingTransformationCreated(
        string $stringX,
        string $stringY,
        int $shiftPointX,
        int $shiftPointY
    ): void {
        $factory = new ShiftingTransformationFactory($this->parametersParser);
        $this->givenStringParametersParser_parseParameters_returnsParametersList(
            new StringList([
                'x' => $stringX,
                'y' => $stringY,
            ])
        );

        /** @var ShiftingTransformation $transformation */
        $transformation = $factory->createTransformation(self::STRING_PARAMETERS);

        $this->assertInstanceOf(ShiftingTransformation::class, $transformation);
        $this->assertStringParametersParser_parseParameters_isCalledOnceWithPatternAndStringParameters(
            self::PARSING_PATTERN,
            self::STRING_PARAMETERS_IN_LOWER_CASE
        );
        $parameters = $transformation->getParameters();
        $this->assertEquals($shiftPointX, $parameters->getX());
        $this->assertEquals($shiftPointY, $parameters->getY());
    }

    public function shiftParametersProvider(): array
    {
        return [
            ['11', '-10', 11, -10],
            ['0', '', 0, 0],
        ];
    }

    private function assertStringParametersParser_parseParameters_isCalledOnceWithPatternAndStringParameters(
        string $pattern,
        string $parameters
    ): void {
        /** @var StringList $parameterNamesList */
        \Phake::verify($this->parametersParser, \Phake::times(1))
            ->parseParameters($pattern, $parameters);
    }

    private function givenStringParametersParser_parseParameters_returnsParametersList(StringList $parametersList): void
    {
        \Phake::when($this->parametersParser)->parseParameters(\Phake::anyParameters())->thenReturn($parametersList);
    }
}
