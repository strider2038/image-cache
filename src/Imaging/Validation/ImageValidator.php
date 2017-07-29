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

use Strider2038\ImgCache\Exception\FileNotFoundException;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageValidator implements ImageValidatorInterface
{
    public function isValidImageMimeType(string $mime): bool
    {
        return in_array($mime, [
            'image/jpeg',
            'image/png',
        ]);
    }

    public function hasFileValidImageMimeType(string $filename): bool
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException("File '{$filename}' not found");
        }

        $mime = mime_content_type($filename);

        return $this->isValidImageMimeType($mime);
    }

    public function hasBlobValidImageMimeType(string $blob): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $blob);
        finfo_close($finfo);

        return $this->isValidImageMimeType($mime);
    }

    public function hasValidImageExtension(string $filename): bool
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        return in_array($ext, ['jpg', 'jpeg', 'png']);
    }

}