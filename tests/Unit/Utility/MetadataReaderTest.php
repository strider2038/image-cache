<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Utility;

use Strider2038\ImgCache\Core\Streaming\ResourceStream;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Utility\MetadataReader;

class MetadataReaderTest extends FileTestCase
{
    private const STREAM_CONTENTS = 'stream contents';
    private const MIME_TYPE_TEXT_PLAIN = 'text/plain';
    private const MIME_TYPE_IMAGE_JPEG = 'image/jpeg';

    /** @test */
    public function getContentTypeFromStream_givenStream_contentTypeReturned(): void
    {
        $reader = $this->createMetadataReader();
        $stream = $this->givenStream();
        $this->givenStream_getContents_returnsString($stream, self::STREAM_CONTENTS);

        $contentType = $reader->getContentTypeFromStream($stream);

        $this->assertStream_getContents_isCalledOnce($stream);
        $this->assertStream_rewind_isCalledOnce($stream);
        $this->assertEquals(self::MIME_TYPE_TEXT_PLAIN, $contentType);
    }

    /** @test */
    public function getContentTypeFromStream_givenFileStream_contentTypeReturned(): void
    {
        $reader = $this->createMetadataReader();
        $stream = $this->givenImageStream();

        $contentType = $reader->getContentTypeFromStream($stream);

        $this->assertEquals(self::MIME_TYPE_IMAGE_JPEG, $contentType);
    }

    private function createMetadataReader(): MetadataReader
    {
        return new MetadataReader();
    }

    private function givenStream(): StreamInterface
    {
        return \Phake::mock(StreamInterface::class);
    }

    private function givenImageStream(): ResourceStream
    {
        $filename = $this->givenAssetFilename(self::IMAGE_BOX_JPG);

        return new ResourceStream(fopen($filename, 'rb'));
    }

    private function givenStream_getContents_returnsString(StreamInterface $stream, string $contents): void
    {
        \Phake::when($stream)->getContents()->thenReturn($contents);
    }

    private function assertStream_getContents_isCalledOnce(StreamInterface $stream): void
    {
        \Phake::verify($stream, \Phake::times(1))->getContents();
    }

    private function assertStream_rewind_isCalledOnce(StreamInterface $stream): void
    {
        \Phake::verify($stream, \Phake::times(1))->rewind();
    }
}
