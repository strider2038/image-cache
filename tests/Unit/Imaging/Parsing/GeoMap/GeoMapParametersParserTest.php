<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\GeoMap;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParameters;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParametersFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParametersParser;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParserInterface;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class GeoMapParametersParserTest extends TestCase
{
    private const FILENAME = 'directory/filename_parameter.jpg';
    private const PATTERN = '/^pattern$/';
    private const BASE_FILENAME = 'filename';
    private const PARAMETER = 'parameter';
    private const LATITUDE_VALUE = 60;
    private const LONGITUDE_VALUE = 40;
    private const IMAGE_FORMAT = 'jpg';

    /** @var StringParametersParserInterface */
    private $parametersParser;

    /** @var GeoMapParametersFactoryInterface */
    private $parametersFactory;

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->parametersParser = \Phake::mock(StringParametersParserInterface::class);
        $this->parametersFactory = \Phake::mock(GeoMapParametersFactoryInterface::class);
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
    }

    /** @test */
    public function parseMapParametersFromFilename_givenFilename_filenameParsedToGeoMapParameters(): void
    {
        $parser = $this->createGeoMapParametersParser([self::PATTERN]);
        $expectedParameters = $this->givenParametersFactory_createGeoMapParameters_returnsGeoMapParameters();
        $this->givenParametersParser_parseParameters_returnsParsedParameters(
            self::PATTERN,
            self::BASE_FILENAME,
            new StringList([
                'latitude' => self::LATITUDE_VALUE
            ])
        );
        $this->givenParametersParser_parseParameters_returnsParsedParameters(
            self::PATTERN,
            self::PARAMETER,
            new StringList([
                'longitude' => self::LONGITUDE_VALUE
            ])
        );

        $parameters = $parser->parseMapParametersFromFilename(self::FILENAME);

        $this->assertInstanceOf(GeoMapParameters::class, $parameters);
        $this->assertParametersFactory_createGeoMapParameters_isCalledOnce();
        $this->assertSame($expectedParameters, $parameters);
        $this->assertParametersParser_parseParameters_isCalledOnceWithPatternAndString(self::PATTERN, self::BASE_FILENAME);
        $this->assertParametersParser_parseParameters_isCalledOnceWithPatternAndString(self::PATTERN, self::PARAMETER);
        $this->assertEquals(self::LATITUDE_VALUE, $parameters->latitude);
        $this->assertEquals(self::LONGITUDE_VALUE, $parameters->longitude);
        $this->assertEquals(self::IMAGE_FORMAT, $parameters->imageFormat);
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            GeoMapParameters::class,
            InvalidRequestValueException::class
        );
    }

    private function createGeoMapParametersParser(array $parsingPatterns): GeoMapParametersParser
    {
        return new GeoMapParametersParser(
            new StringList($parsingPatterns),
            $this->parametersParser,
            $this->parametersFactory,
            $this->validator
        );
    }

    private function assertParametersFactory_createGeoMapParameters_isCalledOnce(): void
    {
        \Phake::verify($this->parametersFactory, \Phake::times(1))->createGeoMapParameters();
    }

    private function givenParametersFactory_createGeoMapParameters_returnsGeoMapParameters(): GeoMapParameters
    {
        $parameters = new GeoMapParameters();
        \Phake::when($this->parametersFactory)->createGeoMapParameters()->thenReturn($parameters);

        return $parameters;
    }

    private function assertParametersParser_parseParameters_isCalledOnceWithPatternAndString($pattern, $string): void
    {
        \Phake::verify($this->parametersParser, \Phake::times(1))->parseParameters($pattern, $string);
    }

    private function givenParametersParser_parseParameters_returnsParsedParameters(
        string $pattern,
        string $stringParameters,
        StringList $parsedParameters
    ): void {
        \Phake::when($this->parametersParser)
            ->parseParameters($pattern, $stringParameters)
            ->thenReturn($parsedParameters);
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
