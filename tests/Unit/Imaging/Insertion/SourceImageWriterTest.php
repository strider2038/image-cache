<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Insertion;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Insertion\SourceImageWriter;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKey;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyParserInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\SourceAccessorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class SourceImageWriterTest extends TestCase
{
    use ProviderTrait;

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

    /**
     * @test
     * @param bool $expectedExists
     * @dataProvider boolValuesProvider
     */
    public function exists_sourceAccessorExistsReturnBool_boolIsReturned(bool $expectedExists): void
    {
        $writer = $this->createSourceImageWriter();
        $publicFilename = self::PUBLIC_FILENAME;
        $this->givenKeyParser_parse_returnsSourceKey();
        $this->givenSourceAccessor_exists_returns($publicFilename, $expectedExists);

        $actualExists = $writer->exists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
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

    /** @test */
    public function getFileMask_givenKey_keyIsParsedAndFileMaskIsReturned(): void
    {
        $writer = $this->createSourceImageWriter();
        $this->givenKeyParser_parse_returnsSourceKey();

        $filename = $writer->getFileNameMask(self::KEY);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertEquals(self::PUBLIC_FILENAME, $filename);
    }

    private function givenKeyParser_parse_returnsSourceKey(): SourceKey
    {
        $parsedKey = \Phake::mock(SourceKey::class);
        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($parsedKey);
        \Phake::when($parsedKey)->getPublicFilename()->thenReturn(self::PUBLIC_FILENAME);

        return $parsedKey;
    }

    private function givenSourceAccessor_exists_returns(string $publicFilename, bool $value): void
    {
        \Phake::when($this->sourceAccessor)->exists($publicFilename)->thenReturn($value);
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
        return new SourceImageWriter($this->keyParser, $this->sourceAccessor);
    }
}
