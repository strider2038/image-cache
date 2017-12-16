<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Driver\WebDAV;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\RequestOptionsFactory;
use Strider2038\ImgCache\Utility\MetadataReaderInterface;

class RequestOptionsFactoryTest extends TestCase
{
    private const CONTENT_TYPE = 'content/type';
    private const STREAM_SIZE = 1;
    private const STREAM_CONTENTS = 'stream contents';

    /** @var MetadataReaderInterface */
    private $metadataReader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metadataReader = \Phake::mock(MetadataReaderInterface::class);
    }

    /** @test */
    public function createPutOptions_givenStream_arrayWithOptionsReturned(): void
    {
        $factory = new RequestOptionsFactory($this->metadataReader);
        $stream = $this->givenStream();
        $this->givenMetadataReader_getContentTypeFromStream_returnsString(self::CONTENT_TYPE);
        $this->givenStream_getSize_returnsInt($stream, self::STREAM_SIZE);
        $this->givenStream_getContents_returnsString($stream, self::STREAM_CONTENTS);

        $options = $factory->createPutOptions($stream);

        $this->assertMetadataReader_getContentTypeFromStream_isCalledOnceWithStream($stream);
        $this->assertStream_getSize_isCalledOnce($stream);
        $this->assertStream_getContents_isCalledOnce($stream);
        $this->assertStream_rewind_isCalledOnce($stream);
        $this->assertArrayHasKey('headers', $options);
        $this->assertArraySubset(['Content-Type' => self::CONTENT_TYPE], $options['headers']);
        $this->assertArraySubset(['Content-Length' => self::STREAM_SIZE], $options['headers']);
        $this->assertArraySubset(['Etag' => md5(self::STREAM_CONTENTS)], $options['headers']);
        $this->assertArraySubset(['Sha256' => hash('sha256', self::STREAM_CONTENTS)], $options['headers']);
        $this->assertArraySubset(['body' => self::STREAM_CONTENTS], $options);
        $this->assertArraySubset(['expect' => true], $options);
    }

    private function givenStream(): StreamInterface
    {
        return \Phake::mock(StreamInterface::class);
    }

    private function givenMetadataReader_getContentTypeFromStream_returnsString(string $contentType): void
    {
        \Phake::when($this->metadataReader)
            ->getContentTypeFromStream(\Phake::anyParameters())
            ->thenReturn($contentType);
    }

    private function assertMetadataReader_getContentTypeFromStream_isCalledOnceWithStream(StreamInterface $stream): void
    {
        \Phake::verify($this->metadataReader, \Phake::times(1))
            ->getContentTypeFromStream($stream);
    }

    private function givenStream_getSize_returnsInt(StreamInterface $stream, int $size): void
    {
        \Phake::when($stream)->getSize()->thenReturn($size);
    }

    private function givenStream_getContents_returnsString(StreamInterface $stream, string $contents): void
    {
        \Phake::when($stream)->getContents()->thenReturn($contents);
    }

    private function assertStream_getSize_isCalledOnce(StreamInterface $stream): void
    {
        \Phake::verify($stream, \Phake::times(1))->getSize();
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
