<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Transformation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParserInterface;
use Strider2038\ImgCache\Imaging\Transformation\ResizeParameters;
use Strider2038\ImgCache\Imaging\Transformation\ResizeTransformation;
use Strider2038\ImgCache\Imaging\Transformation\ResizeTransformationFactory;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeTransformationFactoryTest extends TestCase
{
    private const STRING_PARAMETERS = 'String Parameters';
    private const STRING_PARAMETERS_LOWER_CASE = 'string parameters';
    private const PARSING_PATTERN = '/^(\d+)(x(\d+))?([fswh]{1})?$/';
    private const PARAMETER_NAMES = ['width', 'height', 'mode'];

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
     * @param string $stringWidth
     * @param string $stringHeight
     * @param string $stringMode
     * @param int $expectedWidth
     * @param int $expectedHeight
     * @param string $expectedMode
     * @dataProvider stringParametersAndExpectedParametersProvider
     */
    public function createTransformation_givenStringParameters_resizeTransformationCreatedAndReturned(
        $stringWidth,
        $stringHeight,
        $stringMode,
        $expectedWidth,
        $expectedHeight,
        $expectedMode
    ): void {
        $factory = new ResizeTransformationFactory($this->parametersParser, $this->validator);
        $parametersList = new StringList([
            'width' => $stringWidth,
            'height' => $stringHeight,
            'mode' => $stringMode,
        ]);
        $this->givenStringParametersParser_parseParameters_returnsParametersList($parametersList);

        /** @var ResizeTransformation $transformation */
        $transformation = $factory->createTransformation(self::STRING_PARAMETERS);

        $this->assertInstanceOf(ResizeTransformation::class, $transformation);
        $this->assertStringParametersParser_parseParameters_isCalledOnceWithPatternAndParameterNamesAndStringParameters(
            self::PARSING_PATTERN,
            self::PARAMETER_NAMES,
            self::STRING_PARAMETERS_LOWER_CASE
        );
        $transformationParameters = $transformation->getParameters();
        $this->assertEquals($expectedWidth, $transformationParameters->getWidth());
        $this->assertEquals($expectedHeight, $transformationParameters->getHeight());
        $this->assertEquals($expectedMode, $transformationParameters->getMode()->getValue());
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            ResizeParameters::class,
            InvalidRequestValueException::class
        );
    }

    public function stringParametersAndExpectedParametersProvider(): array
    {
        return [
            [
                '100',
                '150',
                'f',
                100,
                150,
                ResizeModeEnum::FIT_IN,
            ],
            [
                '100',
                '',
                'w',
                100,
                100,
                ResizeModeEnum::PRESERVE_WIDTH,
            ],
            [
                '100',
                '150',
                '',
                100,
                150,
                ResizeModeEnum::STRETCH,
            ],
        ];
    }

    private function assertStringParametersParser_parseParameters_isCalledOnceWithPatternAndParameterNamesAndStringParameters(
        string $pattern,
        array $parameterNames,
        string $parameters
    ): void {
        /** @var StringList$parameterNamesList */
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
