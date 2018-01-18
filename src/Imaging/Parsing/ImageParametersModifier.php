<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing;

use Strider2038\ImgCache\Imaging\Image\ImageParameters;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageParametersModifier implements ImageParametersModifierInterface
{
    private const PARSING_PATTERN = '/^(q|quality)(?P<quality>[0-9]+)$/';

    /** @var StringParametersParserInterface */
    private $parametersParser;

    public function __construct(StringParametersParserInterface $parametersParser)
    {
        $this->parametersParser = $parametersParser;
    }

    public function updateParametersByConfiguration(ImageParameters $parameters, string $stringParameters): void
    {
        $parsedParameters = $this->parametersParser->parseParameters(self::PARSING_PATTERN, $stringParameters);

        if ($parsedParameters->count() !== 0) {
            $quality = (int) $parsedParameters->get('quality');
            $parameters->setQuality($quality);
        }
    }
}
