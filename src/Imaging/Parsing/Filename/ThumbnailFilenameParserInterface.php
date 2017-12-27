<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Filename;

use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilename;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ThumbnailFilenameParserInterface
{
    public function getParsedFilename(string $key): ThumbnailFilename;
}
