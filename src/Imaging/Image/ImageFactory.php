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
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Exception\InvalidMediaTypeException;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageFactory implements ImageFactoryInterface
{
    /** @var ImageParametersFactoryInterface */
    private $saveOptionsFactory;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(
        ImageParametersFactoryInterface $saveOptionsFactory,
        ImageValidatorInterface $imageValidator,
        FileOperationsInterface $fileOperations,
        StreamFactoryInterface $streamFactory
    ) {
        $this->saveOptionsFactory = $saveOptionsFactory;
        $this->imageValidator = $imageValidator;
        $this->fileOperations = $fileOperations;
        $this->streamFactory = $streamFactory;
    }

    public function create(StreamInterface $data, ImageParameters $saveOptions): Image
    {
        if (!$this->imageValidator->hasDataValidImageMimeType($data->getContents())) {
            throw new InvalidMediaTypeException('Image has unsupported mime type');
        }

        return new Image($saveOptions, $data);
    }

    public function createFromFile(string $filename): Image
    {
        if (!$this->fileOperations->isFile($filename)) {
            throw new FileNotFoundException(sprintf('File "%s" not found', $filename));
        }
        if (!$this->imageValidator->hasValidImageExtension($filename)) {
            throw new InvalidMediaTypeException(sprintf('File "%s" has unsupported image extension', $filename));
        }
        if (!$this->imageValidator->hasFileValidImageMimeType($filename)) {
            throw new InvalidMediaTypeException(sprintf('File "%s" has unsupported mime type', $filename));
        }

        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::READ_ONLY);
        $data = $this->fileOperations->openFile($filename, $mode);

        return new Image($this->createSaveOptions(), $data);
    }

    public function createFromData(string $data): Image
    {
        if (!$this->imageValidator->hasDataValidImageMimeType($data)) {
            throw new InvalidMediaTypeException('Image has unsupported mime type');
        }

        $stream = $this->streamFactory->createStreamFromData($data);

        return new Image($this->createSaveOptions(), $stream);
    }

    public function createFromStream(StreamInterface $stream): Image
    {
        if (!$this->imageValidator->hasDataValidImageMimeType($stream->getContents())) {
            throw new InvalidMediaTypeException('Image has unsupported mime type');
        }

        $stream->rewind();

        return new Image($this->createSaveOptions(), $stream);
    }

    private function createSaveOptions(): ImageParameters
    {
        return $this->saveOptionsFactory->createImageParameters();
    }
}
