<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ConfigurationValidator
{
    public const INVALID_DIRECTORY_NAME_MESSAGE =
        'Invalid directory name: name must contain only latin symbols, digits, dots, '
        . 'not duplicating slashes, \'_\' and \'-\'';

    public static function isValidDirectoryName(string $value): bool
    {
        return preg_match('/^[a-z0-9_\-\.\/]+$/i', $value) && !preg_match('/\/{2,}/', $value);
    }
}
