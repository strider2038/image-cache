<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source;

use Strider2038\ImgCache\Imaging\Extraction\ExtractedImageInterface;
use Strider2038\ImgCache\Imaging\Extraction\FileExtractionRequestInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface FileSourceInterface
{
    public function get(FileExtractionRequestInterface $request): ?ExtractedImageInterface;
    public function exists(FileExtractionRequestInterface $request): bool;
}