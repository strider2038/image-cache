<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Utility;

use Strider2038\ImgCache\Core\Streaming\StreamInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class MetadataReader implements MetadataReaderInterface
{
    public function getContentTypeFromStream(StreamInterface $stream): string
    {
        $contents = $stream->getContents();
        $stream->rewind();

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($fileInfo, $contents);
        finfo_close($fileInfo);

        return $mime;
    }
}
