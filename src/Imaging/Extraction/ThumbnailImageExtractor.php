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

use Strider2038\ImgCache\Imaging\Extraction\Request\FileExtractionRequestInterface;
use Strider2038\ImgCache\Imaging\Extraction\Request\ThumbnailRequestConfigurationInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Parsing\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\FileSourceInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailImageExtractor implements ImageExtractorInterface
{
    /** @var FileSourceInterface */
    private $source;

    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var ThumbnailImageFactoryInterface */
    private $thumbnailImageFactory;

    public function __construct(
        FileSourceInterface $source,
        ThumbnailKeyParserInterface $keyParserFactory,
        ThumbnailImageFactoryInterface $thumbnailImageFactory
    ) {
        $this->source = $source;
        $this->keyParser = $keyParserFactory;
        $this->thumbnailImageFactory = $thumbnailImageFactory;
    }

    public function extract(string $key): ?ImageInterface
    {
        /** @var ThumbnailRequestConfigurationInterface $requestConfiguration */
        $requestConfiguration = $this->keyParser->getRequestConfiguration($key);

        /** @var FileExtractionRequestInterface $extractionRequest */
        $extractionRequest = $requestConfiguration->getExtractionRequest();

        /** @var ImageInterface $sourceImage */
        $sourceImage = $this->source->get($extractionRequest);

        if ($sourceImage === null) {
            return null;
        }

        if (!$requestConfiguration->hasTransformations()) {
            return $sourceImage;
        }

        return $this->thumbnailImageFactory->create($requestConfiguration, $sourceImage);
    }

    public function exists(string $key): bool
    {
        /** @var ThumbnailRequestConfigurationInterface $requestConfiguration */
        $requestConfiguration = $this->keyParser->getRequestConfiguration($key);

        /** @var FileExtractionRequestInterface $extractionRequest */
        $extractionRequest = $requestConfiguration->getExtractionRequest();

        return $this->source->exists($extractionRequest);
    }

}