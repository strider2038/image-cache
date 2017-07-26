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

use Strider2038\ImgCache\Imaging\Extraction\Request\ThumbnailRequestConfigurationInterface;
use Strider2038\ImgCache\Imaging\Extraction\Result\ExtractedImageInterface;
use Strider2038\ImgCache\Imaging\Extraction\Result\ThumbnailImage;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ThumbnailImageFactoryInterface
{
    public function create(
        ThumbnailRequestConfigurationInterface $requestConfiguration,
        ExtractedImageInterface $extractedExtractedImage
    ): ThumbnailImage;
}