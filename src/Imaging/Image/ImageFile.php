<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Image;

use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Exception\FileOperationException;
use Strider2038\ImgCache\Exception\InvalidImageException;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageFile extends AbstractImage implements ImageInterface
{
    const EXTENSION_JPG = 'jpg';
    const EXTENSION_JPEG = 'jpeg';
    const EXTENSION_PNG = 'png';
    
    /** @var string */
    private $filename;
    
    public function __construct(string $filename, SaveOptions $saveOptions)
    {
        if (!$this->hasValidMimeType($filename)) {
            throw new InvalidImageException("File '{$filename}' has unsupported mime type");
        }
        $this->filename = $filename;
        parent::__construct($saveOptions);
    }
    
    public function getFilename(): string
    {
        return $this->filename;
    }

    public function saveTo(string $filename): void
    {
        if (!copy($this->filename, $filename)) {
            throw new FileOperationException("Cannot copy file '{$this->filename}' to '{$filename}'");
        }
    }

    public function open(ProcessingEngineInterface $engine): ProcessingImageInterface
    {
        return $engine->openFromFile($this->filename);
    }

    public function render(): void
    {
        // TODO: Implement render() method.
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
