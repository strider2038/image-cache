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

use Strider2038\ImgCache\Core\FileOperations;
use Strider2038\ImgCache\Exception\FileNotFoundException;
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

    public function __construct(string $filename, FileOperations $fileOperations, SaveOptions $saveOptions)
    {
        parent::__construct($fileOperations, $saveOptions);
        if (!$this->fileOperations->isFile($filename)) {
            throw new FileNotFoundException("File {$filename} not found");
        }
        $this->filename = $filename;
    }
    
    public function getFilename(): string
    {
        return $this->filename;
    }

    public function saveTo(string $filename): void
    {
        $this->fileOperations->copyFileTo($this->filename, $filename);
    }

    public function open(ProcessingEngineInterface $engine): ProcessingImageInterface
    {
        $processingImage = $engine->openFromFile($this->filename, $this->saveOptions);

        return $processingImage;
    }

    public function getBlob(): string
    {
        return $this->fileOperations->getFileContents($this->filename);
    }
}
