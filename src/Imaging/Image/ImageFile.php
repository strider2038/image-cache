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
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageFile extends AbstractImage implements ImageInterface
{
    /** @var string */
    private $filename;
    
    public function __construct(string $filename, SaveOptions $saveOptions)
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException("File {$filename} not found");
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
        echo file_get_contents($this->filename);
    }
}
