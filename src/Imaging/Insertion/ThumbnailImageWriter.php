<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Insertion;

use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilename;
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageWriter implements ImageWriterInterface
{
    /** @var ThumbnailFilenameParserInterface */
    private $filenameParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    public function __construct(
        ThumbnailFilenameParserInterface $filenameParser,
        StorageAccessorInterface $storageAccessor
    ) {
        $this->filenameParser = $filenameParser;
        $this->storageAccessor = $storageAccessor;
    }

    public function imageExists(string $filename): bool
    {
        $parsedFilename = $this->getParsedFilename($filename);

        return $this->storageAccessor->imageExists($parsedFilename->getValue());
    }

    public function insertImage(string $filename, Image $image): void
    {
        $parsedFilename = $this->getParsedFilename($filename);
        $this->storageAccessor->putImage($parsedFilename->getValue(), $image);
    }

    public function deleteImage(string $filename): void
    {
        $parsedFilename = $this->getParsedFilename($filename);
        $this->storageAccessor->deleteImage($parsedFilename->getValue());
    }

    public function getImageFileNameMask(string $filename): string
    {
        $parsedFilename = $this->getParsedFilename($filename);

        return $parsedFilename->getMask();
    }

    private function getParsedFilename(string $filename): ThumbnailFilename
    {
        $parsedFilename = $this->filenameParser->getParsedFilename($filename);

        if ($parsedFilename->hasProcessingConfiguration()) {
            throw new InvalidRequestValueException(sprintf(
                'Image name "%s" for source image cannot have process configuration.',
                $filename
            ));
        }

        return $parsedFilename;
    }
}
