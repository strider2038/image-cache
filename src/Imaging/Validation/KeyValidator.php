<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Validation;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class KeyValidator implements KeyValidatorInterface
{
    public function isValidPublicFilename(string $filename): bool
    {
        // slash only or empty
        if (empty($filename) || $filename === '/') {
            return false;
        }
        // incorrect symbols in filename
        if (!preg_match('/^[A-Za-z0-9_\.\/]+$/', $filename)) {
            return false;
        }
        // duplicating slashes
        if (preg_match('/\/{2,}/', $filename)) {
            return false;
        }
        // duplicating dots
        if (preg_match('/\.{2,}/', $filename)) {
            return false;
        }
        // dots are not allowed in directory names
        if (preg_match('/^.*\..*\/.*/', $filename)) {
            return false;
        }

        return true;
    }
}