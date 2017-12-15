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

use Strider2038\ImgCache\Exception\InvalidValueException;
use Strider2038\ImgCache\Imaging\Extraction\ImageExtractorInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Insertion\ImageWriterInterface;
use Strider2038\ImgCache\Imaging\Insertion\NullWriter;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageStorage implements ImageStorageInterface
{
    /** @var ImageExtractorInterface */
    private $imageExtractor;

    /** @var ImageWriterInterface */
    private $imageWriter;

    public function __construct(ImageExtractorInterface $imageExtractor, ImageWriterInterface $imageWriter = null)
    {
        $this->imageExtractor = $imageExtractor;
        $this->imageWriter = $imageWriter ?? new NullWriter();
    }

    public function getImage(ImageFilenameInterface $filename): Image
    {
        $this->validateKey($filename);

        return $this->imageExtractor->extractImage($filename);
    }

    public function putImage(ImageFilenameInterface $filename, Image $image): void
    {
        $this->validateKey($filename);
        $this->imageWriter->insertImage($filename, $image);
    }

    public function imageExists(ImageFilenameInterface $filename): bool
    {
        $this->validateKey($filename);

        return $this->imageWriter->imageExists($filename);
    }

    public function deleteImage(ImageFilenameInterface $filename): void
    {
        $this->validateKey($filename);
        $this->imageWriter->deleteImage($filename);
    }

    public function getImageFileNameMask(ImageFilenameInterface $filename): string
    {
        $this->validateKey($filename);

        return $this->imageWriter->getImageFileNameMask($filename);
    }

    private function validateKey(string $key): void
    {
        if (\strlen($key) <= 0 || $key[0] !== '/') {
            throw new InvalidValueException('Key must start with slash');
        }
    }
}
