<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging;

use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Exception\InvalidImageException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Image
{
    const EXTENSION_JPG = 'jpg';
    const EXTENSION_JPEG = 'jpeg';
    const EXTENSION_PNG = 'png';
    
    /** @var string */
    private $filename;
    
    public function __construct(string $filename)
    {
        if (!$this->hasValidMimeType($filename)) {
            throw new InvalidImageException("File '{$filename}' has unsupported mime type");
        }
        $this->filename = $filename;
    }
    
    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getData()
    {
        
    }
    
    /**
     * @return string[]
     */
    public static function getSupportedMimeTypes(): array
    {
        return [
            // 'image/gif', not implemented yet
            'image/jpeg',
            'image/png',
        ];
    }
    
    public static function hasValidMimeType(string $filename): bool
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException("File '{$filename}' not found");
        }

        $mime = mime_content_type($filename);

        return in_array($mime, static::getSupportedMimeTypes());
    }
}
