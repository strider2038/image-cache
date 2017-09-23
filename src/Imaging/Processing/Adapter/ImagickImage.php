<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing\Adapter;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Imaging\Image\AbstractImage;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

/**
 * @todo Add layer support http://php.net/manual/ru/imagick.coalesceimages.php
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickImage extends AbstractImage implements ProcessingImageInterface
{
    /** @var \Imagick */
    private $imagick;

    /** @var LoggerInterface */
    private $logger;
    
    public function __construct(\Imagick $processor, FileOperationsInterface $fileOperations, SaveOptions $saveOptions)
    {
        parent::__construct($fileOperations, $saveOptions);
        $this->imagick = $processor;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getHeight(): int
    {
        return $this->imagick->getImageHeight();
    }

    public function getWidth(): int
    {
        return $this->imagick->getImageWidth();
    }

    public function crop(int $width, int $height, int $x, int $y): void
    {
        $this->imagick->cropImage($width, $height, $x, $y);
    }

    public function resize(int $width, int $height): void
    {
        $this->imagick->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);
    }

    public function saveTo(string $filename): void
    {
        $directory = dirname($filename);

        if (!$this->fileOperations->isDirectory($directory)) {
            $this->fileOperations->createDirectory($directory);
        }

        $quality = $this->saveOptions->getQuality();

        $this->imagick->setImageCompressionQuality($quality);
        $this->imagick->writeImage($filename);

        $this->logger->info(sprintf(
            'Processed image was saved to file "%" with compression quality %d',
            $filename,
            $quality
        ));
    }

    public function open(ProcessingEngineInterface $engine): ProcessingImageInterface
    {
        $blob = $this->imagick->getImageBlob();
        $image = $engine->openFromBlob($blob, $this->saveOptions);

        return $image;
    }

    public function getBlob(): string
    {
        return $this->imagick->getImageBlob();
    }
}
