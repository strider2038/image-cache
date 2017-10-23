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

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Exception\InvalidMediaTypeException;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Processing\SaveOptionsFactoryInterface;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;

/**
 * @deprecated
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageFactory implements ImageFactoryInterface
{
    /** @var SaveOptionsFactoryInterface */
    private $saveOptionsFactory;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var FileOperationsInterface */
    private $fileOperations;

    public function __construct(
        SaveOptionsFactoryInterface $saveOptionsFactory,
        ImageValidatorInterface $imageValidator,
        FileOperationsInterface $fileOperations
    ) {
        $this->saveOptionsFactory = $saveOptionsFactory;
        $this->imageValidator = $imageValidator;
        $this->fileOperations = $fileOperations;
    }

    public function createImageFile(string $filename): ImageFile
    {
        if (!$this->imageValidator->hasValidImageExtension($filename)) {
            throw new InvalidMediaTypeException("File '{$filename}' has unsupported image extension");
        }
        if (!$this->imageValidator->hasFileValidImageMimeType($filename)) {
            throw new InvalidMediaTypeException("File '{$filename}' has unsupported mime type");
        }

        $image = new ImageFile($filename, $this->fileOperations, $this->createSaveOptions());

        return $image;
    }

    public function createImageBlob(string $blob): ImageBlob
    {
        if (!$this->imageValidator->hasBlobValidImageMimeType($blob)) {
            throw new InvalidMediaTypeException('Image has unsupported mime type');
        }

        return new ImageBlob($blob, $this->fileOperations, $this->createSaveOptions());
    }

    private function createSaveOptions(): SaveOptions
    {
        return $this->saveOptionsFactory->create();
    }
}
