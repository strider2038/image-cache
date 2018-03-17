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

use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilenameParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SourceImageWriter implements ImageWriterInterface
{
    /** @var PlainFilenameParserInterface */
    private $filenameParser;
    /** @var StorageAccessorInterface */
    private $storageAccessor;

    public function __construct(
        PlainFilenameParserInterface $filenameParser,
        StorageAccessorInterface $storageAccessor
    ) {
        $this->filenameParser = $filenameParser;
        $this->storageAccessor = $storageAccessor;
    }

    public function imageExists(string $filename): bool
    {
        $parsedFilename = $this->filenameParser->getParsedFilename($filename);

        return $this->storageAccessor->imageExists($parsedFilename->getValue());
    }

    public function insertImage(string $filename, Image $image): void
    {
        $parsedFilename = $this->filenameParser->getParsedFilename($filename);
        $this->storageAccessor->putImage($parsedFilename->getValue(), $image);
    }

    public function deleteImage(string $filename): void
    {
        $parsedFilename = $this->filenameParser->getParsedFilename($filename);
        $this->storageAccessor->deleteImage($parsedFilename->getValue());
    }

    public function deleteDirectoryContents(string $directory): void
    {
        $this->storageAccessor->deleteDirectoryContents($directory);
    }

    public function getImageFileNameMask(string $filename): string
    {
        $parsedFilename = $this->filenameParser->getParsedFilename($filename);

        return $parsedFilename->getValue();
    }
}
