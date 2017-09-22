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
use Strider2038\ImgCache\Imaging\Insertion\SourceImageWriter;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyInterface;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;

class SourceImageWriterTest extends TestCase
{
    private const KEY = 'key';
    private const PUBLIC_FILENAME = 'public_filename';

    /** @var SourceKeyParserInterface */
    private $keyParser;

    /** @var SourceAccessorInterface */
    private $sourceAccessor;

    protected function setUp()
    {
        $this->keyParser = \Phake::mock(SourceKeyParserInterface::class);
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

    /** @test */
    public function delete_givenKey_keyIsParsedAndSourceAccessorDeleteIsCalled(): void
    {
        $writer = $this->createSourceImageWriter();
        $this->givenKeyParser_parse_returnsSourceKey();

        $writer->delete(self::KEY);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertSourceAccessor_delete_isCalledOnce();
    }

    private function givenKeyParser_parse_returnsSourceKey(): SourceKeyInterface
    {
        $parsedKey = \Phake::mock(SourceKeyInterface::class);
        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($parsedKey);
        \Phake::when($parsedKey)->getPublicFilename()->thenReturn(self::PUBLIC_FILENAME);

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

    private function createSourceImageWriter(): SourceImageWriter
    {
        $writer = new SourceImageWriter($this->keyParser, $this->sourceAccessor);

        return $writer;
    }
}
