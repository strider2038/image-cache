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
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParserInterface;
use Strider2038\ImgCache\Imaging\Processing\Transforming\RotatingTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\RotatingTransformationFactory;
use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Processing\Transforming\RotationParameters;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class RotatingTransformationFactoryTest extends TestCase
{
    private const STRING_PARAMETERS = 'String Parameters';
    private const STRING_PARAMETERS_IN_LOWER_CASE = 'string parameters';
    private const PARSING_PATTERN = '/^(?P<degree>-?\d*\.?\d*)$/';
    private const PARAMETER_NAMES = ['degree'];

    /** @var StringParametersParserInterface */
    private $parametersParser;

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->parametersParser = \Phake::mock(StringParametersParserInterface::class);
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
    }

    /**
     * @test
     * @param string $stringDegree
     * @param float $rotationDegree
     * @dataProvider rotationParametersProvider
     */
    public function createTransformation_givenStringParameters_rotatingTransformationCreatedAndReturned(
        string $stringDegree,
        float $rotationDegree
    ): void {
        $factory = new RotatingTransformationFactory($this->parametersParser, $this->validator);
        $this->givenStringParametersParser_parseParameters_returnsParametersList(new StringList([
            'degree' => $stringDegree
        ]));

        /** @var RotatingTransformation $transformation */
        $transformation = $factory->createTransformation(self::STRING_PARAMETERS);

        $this->assertInstanceOf(RotatingTransformation::class, $transformation);
        $this->assertStringParametersParser_parseParameters_isCalledOnceWithPatternAndParameterNamesAndStringParameters(
            self::PARSING_PATTERN,
            self::PARAMETER_NAMES,
            self::STRING_PARAMETERS_IN_LOWER_CASE
        );
        $parameters = $transformation->getParameters();
        $this->assertEquals($rotationDegree, $parameters->getDegree());
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            RotationParameters::class,
            InvalidRequestValueException::class
        );
    }

    public function rotationParametersProvider(): array
    {
        return [
            ['-34.4', -34.4],
            ['', 0],
        ];
    }

    private function assertStringParametersParser_parseParameters_isCalledOnceWithPatternAndParameterNamesAndStringParameters(
        string $pattern,
        array $parameterNames,
        string $parameters
    ): void {
        /** @var StringList $parameterNamesList */
        \Phake::verify($this->parametersParser, \Phake::times(1))
            ->parseParameters($pattern, \Phake::capture($parameterNamesList), $parameters);
        $this->assertEquals($parameterNames, $parameterNamesList->toArray());
    }

    private function givenStringParametersParser_parseParameters_returnsParametersList(StringList $parametersList): void
    {
        \Phake::when($this->parametersParser)->parseParameters(\Phake::anyParameters())->thenReturn($parametersList);
    }

    private function assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
        string $entityClass,
        string $exceptionClass
    ): void {
        \Phake::verify($this->validator, \Phake::times(1))
            ->validateWithException(\Phake::capture($entity), \Phake::capture($exception));
        $this->assertInstanceOf($entityClass, $entity);
        $this->assertEquals($exceptionClass, $exception);
    }
}
