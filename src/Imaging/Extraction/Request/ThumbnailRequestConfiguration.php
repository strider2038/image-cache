<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Extraction\Request;

use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;

/**
 * Request for retrieving image from cache
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailRequestConfiguration implements ThumbnailRequestConfigurationInterface
{
    /** @var FileExtractionRequestInterface */
    private $extractionRequest;
    
    /** @var TransformationsCollection */
    private $transformations;

    /** @var SaveOptions */
    private $saveOptions;

    public function __construct(
        FileExtractionRequestInterface $extractionRequest,
        TransformationsCollection $transformations,
        SaveOptions $saveOptions
    ) {
        $this->extractionRequest = $extractionRequest;
        $this->transformations = $transformations;
        $this->saveOptions = $saveOptions;
    }
    
    public function getExtractionRequest(): FileExtractionRequestInterface
    {
        return $this->extractionRequest;
    }

    public function getTransformations(): TransformationsCollection
    {
        return $this->transformations;
    }

    public function hasTransformations(): bool
    {
        return $this->transformations->count() > 0;
    }

    public function getSaveOptions(): SaveOptions
    {
        return $this->saveOptions;
    }
}
