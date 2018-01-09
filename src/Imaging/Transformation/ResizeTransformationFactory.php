<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Transformation;

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParserInterface;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeTransformationFactory implements TransformationFactoryInterface
{
    private const PARSING_PATTERN = '/^(\d+)(x(\d+))?([fswh]{1})?$/';
    private const PARAMETER_NAMES = ['width', 'height', 'mode'];

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
        $parametersList = $this->parametersParser->parseParameters(
            self::PARSING_PATTERN,
            new StringList(self::PARAMETER_NAMES),
            strtolower($stringParameters)
        );

        $width = (int) $parametersList->get('width');
        $height = (int) $parametersList->get('height');
        $height = $height === 0 ? $width : $height;
        $modeCode = $parametersList->get('mode');
        $mode = new ResizeModeEnum(ResizeModeEnum::isValid($modeCode) ? $modeCode : ResizeModeEnum::STRETCH);

        $parameters = new ResizeParameters($width, $height, $mode);

        $this->validator->validateWithException($parameters, InvalidRequestValueException::class);

        return new ResizeTransformation($parameters);
    }
}
