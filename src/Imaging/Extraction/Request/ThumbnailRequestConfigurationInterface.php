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
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ThumbnailRequestConfigurationInterface
{
    public function getExtractionRequest(): FileExtractionRequestInterface;

    public function getTransformations(): ?TransformationsCollection;

    public function hasTransformations(): bool;

    public function getSaveOptions(): ?SaveOptions;
}
