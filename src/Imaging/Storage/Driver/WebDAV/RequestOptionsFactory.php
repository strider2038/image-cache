<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV;

use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Utility\MetadataReaderInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RequestOptionsFactory implements RequestOptionsFactoryInterface
{
    /** @var MetadataReaderInterface */
    private $metadataReader;

    public function __construct(MetadataReaderInterface $metadataReader)
    {
        $this->metadataReader = $metadataReader;
    }

    public function createPutOptions(StreamInterface $stream): array
    {
        $contentType = $this->metadataReader->getContentTypeFromStream($stream);
        $size = $stream->getSize();
        $contents = $stream->getContents();
        $stream->rewind();

        return [
            'headers' => [
                'Content-Type' => $contentType,
                'Content-Length' => $size,
                'Etag' => md5($contents),
                'Sha256' => hash('sha256', $contents),
            ],
            'body' => $contents,
            'expect' => true,
        ];
    }
}
