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

use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
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

    public function exists(string $key): bool
    {
        $thumbnailKey = $this->keyParser->parse($key);
        return $this->sourceAccessor->exists($thumbnailKey->getPublicFilename());
    }

    public function insert(string $key, StreamInterface $data): void
    {
        $parsedKey = $this->keyParser->parse($key);
        if ($parsedKey->hasProcessingConfiguration()) {
            throw new InvalidRequestValueException(sprintf(
                "Image name '%s' for source image cannot have process configuration",
                $key
            ));
        }

        $this->sourceAccessor->put($parsedKey->getPublicFilename(), $data);
    }

    public function delete(string $key): void
    {
        $parsedKey = $this->keyParser->parse($key);
        if ($parsedKey->hasProcessingConfiguration()) {
            throw new InvalidRequestValueException(sprintf(
                "Image name '%s' for source image cannot have process configuration",
                $key
            ));
        }

        $this->sourceAccessor->delete($parsedKey->getPublicFilename());
    }
}
