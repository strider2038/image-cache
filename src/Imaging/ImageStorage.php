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

    public function getImage(string $key): Image
    {
        $this->validateKey($key);

        return $this->imageExtractor->extractImage($key);
    }

    public function putImage(string $key, Image $image): void
    {
        $this->validateKey($key);
        $data = $image->getData();
        $this->imageWriter->insert($key, $data);
    }

    public function imageExists(string $key): bool
    {
        $this->validateKey($key);

        return $this->imageWriter->exists($key);
    }

    public function deleteImage(string $key): void
    {
        $this->validateKey($key);
        $this->imageWriter->delete($key);
    }

    public function getImageFileNameMask(string $key): string
    {
        $this->validateKey($key);

        return $this->imageWriter->getFileNameMask($key);
    }

    private function validateKey(string $key): void
    {
        if (\strlen($key) <= 0 || $key[0] !== '/') {
            throw new InvalidValueException('Key must start with slash');
        }
    }
}
