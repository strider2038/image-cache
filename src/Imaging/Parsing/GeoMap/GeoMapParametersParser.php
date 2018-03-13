<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\GeoMap;

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParserInterface;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GeoMapParametersParser implements GeoMapParametersParserInterface
{
    private const PARAMETERS_DELIMITER = '_';

    /** @var StringList */
    private $parsingPatterns;
    /** @var StringParametersParserInterface */
    private $parametersParser;
    /** @var GeoMapParametersFactoryInterface */
    private $parametersFactory;
    /** @var EntityValidatorInterface */
    private $validator;

    /** @var GeoMapParameters */
    private $parameters;

    /** @var array */
    private $parameterTypeCasting = [
        'type' => 'strval',
        'latitude' => 'floatval',
        'longitude' => 'floatval',
        'zoom' => 'intval',
        'width' => 'intval',
        'height' => 'intval',
        'scale' => 'floatval',
    ];

    public function __construct(
        StringList $parsingPatterns,
        StringParametersParserInterface $parametersParser,
        GeoMapParametersFactoryInterface $parametersFactory,
        EntityValidatorInterface $validator
    ) {
        $this->parsingPatterns = $parsingPatterns;
        $this->parametersParser = $parametersParser;
        $this->parametersFactory = $parametersFactory;
        $this->validator = $validator;
    }

    public function parseMapParametersFromFilename(string $filename): GeoMapParameters
    {
        $this->parameters = $this->parametersFactory->createGeoMapParameters();

        $this->parameters->imageFormat = pathinfo($filename, PATHINFO_EXTENSION);

        $rawParameters = $this->getRawParametersFromFilename($filename);
        foreach ($rawParameters as $rawParameter) {
            $this->parseAndUpdateParameters($rawParameter);
        }

        $this->validator->validateWithException($this->parameters, InvalidRequestValueException::class);

        return $this->parameters;
    }

    private function getRawParametersFromFilename(string $filename): array
    {
        $baseFilename = pathinfo($filename, PATHINFO_FILENAME);

        return explode(self::PARAMETERS_DELIMITER, $baseFilename);
    }

    private function parseAndUpdateParameters(string $rawParameter): void
    {
        foreach ($this->parsingPatterns as $pattern) {
            $parsedParameters = $this->parametersParser->parseParameters($pattern, $rawParameter);
            $this->updateParametersByParsedValues($parsedParameters);
        }
    }

    private function updateParametersByParsedValues(StringList $parsedParameters): void
    {
        foreach ($parsedParameters as $name => $value) {
            $this->parameters->$name = $this->parameterTypeCasting[$name]($value);
        }
    }
}
