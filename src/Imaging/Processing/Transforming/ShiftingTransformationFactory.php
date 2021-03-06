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

use Strider2038\ImgCache\Imaging\Parsing\StringParametersParserInterface;
use Strider2038\ImgCache\Imaging\Processing\Point;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ShiftingTransformationFactory implements TransformationFactoryInterface
{
    private const PARSING_PATTERN = '/^(x(?P<x>-?\d*))?(y(?P<y>-?\d*))?$/';

    /** @var StringParametersParserInterface */
    private $parametersParser;

    public function __construct(StringParametersParserInterface $parametersParser)
    {
        $this->parametersParser = $parametersParser;
    }

    public function createTransformation(string $stringParameters): TransformationInterface
    {
        $parametersList = $this->parametersParser->strictlyParseParameters(
            self::PARSING_PATTERN,
            strtolower($stringParameters)
        );

        $shiftingPoint = new Point(
            (int) $parametersList->get('x'),
            (int) $parametersList->get('y')
        );

        return new ShiftingTransformation($shiftingPoint);
    }
}
