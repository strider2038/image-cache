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
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SourceImageWriter implements ImageWriterInterface
{
    /** @var SourceKeyParserInterface */
    private $keyParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    public function __construct(SourceKeyParserInterface $keyParser, StorageAccessorInterface $storageAccessor)
    {
        $this->keyParser = $keyParser;
        $this->storageAccessor = $storageAccessor;
    }

    public function imageExists(string $key): bool
    {
        $parsedKey = $this->keyParser->parse($key);
        return $this->storageAccessor->imageExists($parsedKey->getPublicFilename());
    }

    public function insertImage(string $key, Image $image): void
    {
        $parsedKey = $this->keyParser->parse($key);
        $this->storageAccessor->putImage($parsedKey->getPublicFilename(), $image);
    }

    public function deleteImage(string $key): void
    {
        $parsedKey = $this->keyParser->parse($key);
        $this->storageAccessor->deleteImage($parsedKey->getPublicFilename());
    }

    public function getImageFileNameMask(string $key): string
    {
        $parsedKey = $this->keyParser->parse($key);
        return $parsedKey->getPublicFilename();
    }
}
