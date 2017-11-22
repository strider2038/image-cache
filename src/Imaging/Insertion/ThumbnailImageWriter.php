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
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKey;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageWriter implements ImageWriterInterface
{
    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    public function __construct(ThumbnailKeyParserInterface $keyParser, SourceAccessorInterface $sourceAccessor)
    {
        $this->keyParser = $keyParser;
        $this->sourceAccessor = $sourceAccessor;
    }

    public function imageExists(string $key): bool
    {
        $parsedKey = $this->parseKey($key);
        return $this->sourceAccessor->imageExists($parsedKey->getPublicFilename());
    }

    public function insertImage(string $key, Image $image): void
    {
        $parsedKey = $this->parseKey($key);
        $this->sourceAccessor->putImage($parsedKey->getPublicFilename(), $image);
    }

    public function deleteImage(string $key): void
    {
        $parsedKey = $this->parseKey($key);
        $this->sourceAccessor->deleteImage($parsedKey->getPublicFilename());
    }

    public function getImageFileNameMask(string $key): string
    {
        $parsedKey = $this->parseKey($key);
        return $parsedKey->getThumbnailMask();
    }

    private function parseKey(string $key): ThumbnailKey
    {
        $parsedKey = $this->keyParser->parse($key);
        if ($parsedKey->hasProcessingConfiguration()) {
            throw new InvalidRequestValueException(sprintf(
                'Image name "%s" for source image cannot have process configuration',
                $key
            ));
        }

        return $parsedKey;
    }
}
