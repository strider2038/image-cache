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

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class StringParametersParser implements StringParametersParserInterface
{
    public function parseParameters(string $pattern, string $string): StringList
    {
        $isValid = preg_match_all($pattern, $string, $matches);

        if (!$isValid) {
            throw new InvalidRequestValueException(
                sprintf(
                    'Given invalid parameter value: %s',
                    $string
                )
            );
        }

        $values = new StringList();

        foreach ($matches as $key => $match) {
            if (\is_string($key)) {
                $values->set($key, $match[0]);
            }
        }

        return $values;
    }
}
