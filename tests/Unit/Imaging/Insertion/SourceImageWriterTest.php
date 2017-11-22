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
use Strider2038\ImgCache\Imaging\Image\Image;
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
        $this->givenSourceAccessor_imageExists_returns($publicFilename, $expectedExists);

        $actualExists = $writer->imageExists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
    }

    /** @test */
    public function insert_givenKeyAndData_keyIsParsedAndSourceAccessorPutIsCalled(): void
    {
        $writer = $this->createSourceImageWriter();
        $image = \Phake::mock(Image::class);
        $this->givenKeyParser_parse_returnsSourceKey();

        $writer->insertImage(self::KEY, $image);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertSourceAccessor_putImage_isCalledOnceWith($image);
    }

    /** @test */
    public function delete_givenKey_keyIsParsedAndSourceAccessorDeleteIsCalled(): void
    {
        $writer = $this->createSourceImageWriter();
        $this->givenKeyParser_parse_returnsSourceKey();

        $writer->deleteImage(self::KEY);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertSourceAccessor_deleteImage_isCalledOnce();
    }

    /** @test */
    public function getFileMask_givenKey_keyIsParsedAndFileMaskIsReturned(): void
    {
        $writer = $this->createSourceImageWriter();
        $this->givenKeyParser_parse_returnsSourceKey();

        $filename = $writer->getImageFileNameMask(self::KEY);

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

    private function givenSourceAccessor_imageExists_returns(string $publicFilename, bool $value): void
    {
        \Phake::when($this->sourceAccessor)->imageExists($publicFilename)->thenReturn($value);
    }

    private function assertKeyParser_parse_isCalledOnce(): void
    {
        \Phake::verify($this->keyParser, \Phake::times(1))->parse(self::KEY);
    }

    private function assertSourceAccessor_putImage_isCalledOnceWith(Image $image): void
    {
        \Phake::verify($this->sourceAccessor, \Phake::times(1))
            ->putImage(self::PUBLIC_FILENAME, $image);
    }

    private function assertSourceAccessor_deleteImage_isCalledOnce(): void
    {
        \Phake::verify($this->sourceAccessor, \Phake::times(1))
            ->deleteImage(self::PUBLIC_FILENAME);
    }

    private function createSourceImageWriter(): SourceImageWriter
    {
        return new SourceImageWriter($this->keyParser, $this->sourceAccessor);
    }
}
