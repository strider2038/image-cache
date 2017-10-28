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
interface ImageValidatorInterface
{
    public function isValidImageMimeType(string $mime): bool;
    public function hasFileValidImageMimeType(string $filename): bool;
    public function hasDataValidImageMimeType(string $data): bool;
    public function hasValidImageExtension(string $filename): bool;
}
