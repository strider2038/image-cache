<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Extraction;

use Strider2038\ImgCache\Imaging\Parsing\ThumbnailKeyParserFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\FileSourceInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageExtractor implements ImageExtractorInterface
{
    /** @var FileSourceInterface */
    private $source;

    /** @var ThumbnailKeyParserFactoryInterface */
    private $keyParserFactory;

    /** @var ThumbnailImageFactoryInterface */
    private $thumbnailImageFactory;

    public function __construct(
        FileSourceInterface $source,
        ThumbnailKeyParserFactoryInterface $keyParserFactory,
        ThumbnailImageFactoryInterface $thumbnailImageFactory
    ) {
        $this->source = $source;
        $this->keyParserFactory = $keyParserFactory;
        $this->thumbnailImageFactory = $thumbnailImageFactory;
    }

    public function extract(string $key): ?ExtractedImageInterface
    {
        /** @var ThumbnailKeyParserInterface $keyParser */
        $keyParser = $this->keyParserFactory->create($key);

        /** @var FileExtractionRequestInterface $extractionRequest */
        $extractionRequest = $keyParser->getExtractionRequest();

        /** @var ExtractedImageInterface $sourceImage */
        $sourceImage = $this->source->get($extractionRequest);

        if ($sourceImage === null) {
            return null;
        }

        if (!$keyParser->hasTransformations()) {
            return $sourceImage;
        }

        return $this->thumbnailImageFactory->create($keyParser, $sourceImage);
    }

    public function exists(string $key): bool
    {
        /** @var ThumbnailKeyParserInterface $keyParser */
        $keyParser = $this->keyParserFactory->create($key);

        /** @var FileExtractionRequestInterface $extractionRequest */
        $extractionRequest = $keyParser->getExtractionRequest();

        return $this->source->exists($extractionRequest);
    }

}