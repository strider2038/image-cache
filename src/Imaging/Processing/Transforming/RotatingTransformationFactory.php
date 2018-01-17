<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing\Transforming;

use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParserInterface;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RotatingTransformationFactory implements TransformationFactoryInterface
{
    private const PARSING_PATTERN = '/^(?P<degree>-?\d*\.?\d*)$/';

    /** @var StringParametersParserInterface */
    private $parametersParser;

    /** @var EntityValidatorInterface */
    private $validator;

    public function __construct(StringParametersParserInterface $parametersParser, EntityValidatorInterface $validator)
    {
        $this->parametersParser = $parametersParser;
        $this->validator = $validator;
    }

    public function createTransformation(string $stringParameters): TransformationInterface
    {
        $parametersList = $this->parametersParser->strictlyParseParameters(
            self::PARSING_PATTERN,
            strtolower($stringParameters)
        );

        $parameters = new RotationParameters((float) $parametersList->get('degree'));

        $this->validator->validateWithException($parameters, InvalidRequestValueException::class);

        return new RotatingTransformation($parameters);
    }
}
