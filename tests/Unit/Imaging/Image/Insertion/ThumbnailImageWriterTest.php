<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Image\Insertion;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Insertion\ThumbnailImageWriter;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;

class ThumbnailImageWriterTest extends TestCase
{
    private const KEY = 'key';
    private const PUBLIC_FILENAME = 'public_filename';

    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    protected function setUp()
    {
        $this->keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        $this->sourceAccessor = \Phake::mock(SourceAccessorInterface::class);
    }

    /** @test */
    public function insert_givenKeyAndData_keyIsParsedAndSourceAccessorPutIsCalled(): void
    {
        $writer = $this->createSourceImageWriter();
        $stream = \Phake::mock(StreamInterface::class);
        $this->givenKeyParser_parse_returnsSourceKey();

        $writer->insert(self::KEY, $stream);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertSourceAccessor_put_isCalledOnceWith($stream);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp /Image name .* for source image cannot have process configuration/
     */
    public function insert_givenKeyHasProcessingConfiguration_exceptionThrown(): void
    {
        $writer = $this->createSourceImageWriter();
        $stream = \Phake::mock(StreamInterface::class);
        $this->givenKeyParser_parse_returnsSourceKey(true);

        $writer->insert(self::KEY, $stream);
    }

    /** @test */
    public function delete_givenKey_keyIsParsedAndSourceAccessorDeleteIsCalled(): void
    {
        $writer = $this->createSourceImageWriter();
        $this->givenKeyParser_parse_returnsSourceKey();

        $writer->delete(self::KEY);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertSourceAccessor_delete_isCalledOnce();
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp /Image name .* for source image cannot have process configuration/
     */
    public function delete_givenKeyHasProcessingConfiguration_exceptionThrown(): void
    {
        $writer = $this->createSourceImageWriter();
        $this->givenKeyParser_parse_returnsSourceKey(true);

        $writer->delete(self::KEY);
    }

    private function givenKeyParser_parse_returnsSourceKey(
        bool $hasProcessingConfiguration = false
    ): ThumbnailKeyInterface {
        $parsedKey = \Phake::mock(ThumbnailKeyInterface::class);
        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($parsedKey);
        \Phake::when($parsedKey)->getPublicFilename()->thenReturn(self::PUBLIC_FILENAME);
        \Phake::when($parsedKey)->hasProcessingConfiguration()->thenReturn($hasProcessingConfiguration);

        return $parsedKey;
    }

    private function assertKeyParser_parse_isCalledOnce(): void
    {
        \Phake::verify($this->keyParser, \Phake::times(1))->parse(self::KEY);
    }

    private function assertSourceAccessor_put_isCalledOnceWith(StreamInterface $stream): void
    {
        \Phake::verify($this->sourceAccessor, \Phake::times(1))
            ->put(self::PUBLIC_FILENAME, $stream);
    }

    private function assertSourceAccessor_delete_isCalledOnce(): void
    {
        \Phake::verify($this->sourceAccessor, \Phake::times(1))
            ->delete(self::PUBLIC_FILENAME);
    }

    private function createSourceImageWriter(): ThumbnailImageWriter
    {
        $writer = new ThumbnailImageWriter($this->keyParser, $this->sourceAccessor);

        return $writer;
    }
}
