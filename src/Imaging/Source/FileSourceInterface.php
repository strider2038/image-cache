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

use Strider2038\ImgCache\Imaging\Extraction\Request\FileExtractionRequestInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface FileSourceInterface
{
    public function get(FileExtractionRequestInterface $request): ?ImageInterface;
    public function exists(FileExtractionRequestInterface $request): bool;
}