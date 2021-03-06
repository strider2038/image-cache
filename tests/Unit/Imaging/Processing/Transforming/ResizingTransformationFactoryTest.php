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

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParserInterface;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ResizeParameters;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ResizingTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ResizingTransformationFactory;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizingTransformationFactoryTest extends TestCase
{
    private const STRING_PARAMETERS = 'String Parameters';
    private const STRING_PARAMETERS_IN_LOWER_CASE = 'string parameters';
    private const PARSING_PATTERN = '/^(?P<width>\d+)(x(?P<height>\d+))?(?P<mode>[fswh]{1})?$/';

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
        $factory = new ResizingTransformationFactory($this->parametersParser, $this->validator);
        $parametersList = new StringList([
            'width' => $stringWidth,
            'height' => $stringHeight,
            'mode' => $stringMode,
        ]);
        $this->givenStringParametersParser_strictlyParseParameters_returnsParametersList($parametersList);

        /** @var ResizingTransformation $transformation */
        $transformation = $factory->createTransformation(self::STRING_PARAMETERS);

        $this->assertInstanceOf(ResizingTransformation::class, $transformation);
        $this->assertStringParametersParser_strictlyParseParameters_isCalledOnceWithPatternAndStringParameters(
            self::PARSING_PATTERN,
            self::STRING_PARAMETERS_IN_LOWER_CASE
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

    private function assertStringParametersParser_strictlyParseParameters_isCalledOnceWithPatternAndStringParameters(
        string $pattern,
        string $parameters
    ): void {
        /** @var StringList $parameterNamesList */
        \Phake::verify($this->parametersParser, \Phake::times(1))
            ->strictlyParseParameters($pattern, $parameters);
    }

    private function givenStringParametersParser_strictlyParseParameters_returnsParametersList(
        StringList $parametersList
    ): void {
        \Phake::when($this->parametersParser)
            ->strictlyParseParameters(\Phake::anyParameters())
            ->thenReturn($parametersList);
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
