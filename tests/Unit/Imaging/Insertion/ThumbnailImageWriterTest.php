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
use Strider2038\ImgCache\Imaging\Insertion\ThumbnailImageWriter;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKey;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\StorageAccessorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ThumbnailImageWriterTest extends TestCase
{
    use ProviderTrait;

    private const KEY = 'key';
    private const PUBLIC_FILENAME = 'public_filename';
    private const THUMBNAIL_MASK = 'thumbnail_mask';

    /** @var ThumbnailKeyParserInterface */
    private $keyParser;

    /** @var StorageAccessorInterface */
    private $storageAccessor;

    protected function setUp(): void
    {
        $this->keyParser = \Phake::mock(ThumbnailKeyParserInterface::class);
        $this->storageAccessor = \Phake::mock(StorageAccessorInterface::class);
    }

    /**
     * @test
     * @param bool $expectedExists
     * @dataProvider boolValuesProvider
     */
    public function imageExists_sourceImageExtractorExistsReturnsBool_boolIsReturned(bool $expectedExists): void
    {
        $writer = $this->createThumbnailImageWriter();
        $this->givenKeyParser_parse_returnsThumbnailKey();
        $this->givenStorageAccessor_imageExists_returns($expectedExists);

        $actualExists = $writer->imageExists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
    }

    /** @test */
    public function insertImage_givenKeyAndData_keyIsParsedAndSourceAccessorPutIsCalled(): void
    {
        $writer = $this->createThumbnailImageWriter();
        $image = \Phake::mock(Image::class);
        $this->givenKeyParser_parse_returnsThumbnailKey();

        $writer->insertImage(self::KEY, $image);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertStorageAccessor_putImage_isCalledOnceWith($image);
    }

    /** @test */
    public function deleteImage_givenKey_keyIsParsedAndSourceAccessorDeleteIsCalled(): void
    {
        $writer = $this->createThumbnailImageWriter();
        $this->givenKeyParser_parse_returnsThumbnailKey();

        $writer->deleteImage(self::KEY);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertStorageAccessor_deleteImage_isCalledOnce();
    }

    /** @test */
    public function getImageFileNameMask_givenKey_keyIsParsedAndThumbnailMaskIsReturned(): void
    {
        $writer = $this->createThumbnailImageWriter();
        $this->givenKeyParser_parse_returnsThumbnailKey();

        $filename = $writer->getImageFileNameMask(self::KEY);

        $this->assertKeyParser_parse_isCalledOnce();
        $this->assertEquals(self::THUMBNAIL_MASK, $filename);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessageRegExp /Image name .* for source image cannot have process configuration/
     * @param string $method
     * @param array $parameters
     * @dataProvider methodAndParametersProvider
     */
    public function method_givenKeyHasProcessingConfiguration_exceptionThrown(
        string $method,
        array $parameters
    ): void {
        $writer = $this->createThumbnailImageWriter();
        $this->givenKeyParser_parse_returnsThumbnailKey(true);

        call_user_func_array([$writer, $method], $parameters);
    }

    public function methodAndParametersProvider(): array
    {
        return [
            ['imageExists', [self::KEY]],
            ['insertImage', [self::KEY, \Phake::mock(Image::class)]],
            ['deleteImage', [self::KEY]],
            ['getImageFileNameMask', [self::KEY]],
        ];
    }

    private function givenKeyParser_parse_returnsThumbnailKey(
        bool $hasProcessingConfiguration = false
    ): ThumbnailKey {
        $parsedKey = \Phake::mock(ThumbnailKey::class);
        \Phake::when($this->keyParser)->parse(self::KEY)->thenReturn($parsedKey);
        \Phake::when($parsedKey)->getPublicFilename()->thenReturn(self::PUBLIC_FILENAME);
        \Phake::when($parsedKey)->getThumbnailMask()->thenReturn(self::THUMBNAIL_MASK);
        \Phake::when($parsedKey)->hasProcessingConfiguration()->thenReturn($hasProcessingConfiguration);

        return $parsedKey;
    }

    private function givenStorageAccessor_imageExists_returns(bool $value): void
    {
        \Phake::when($this->storageAccessor)->imageExists(self::PUBLIC_FILENAME)->thenReturn($value);
    }

    private function assertKeyParser_parse_isCalledOnce(): void
    {
        \Phake::verify($this->keyParser, \Phake::times(1))->parse(self::KEY);
    }

    private function assertStorageAccessor_putImage_isCalledOnceWith(Image $image): void
    {
        \Phake::verify($this->storageAccessor, \Phake::times(1))
            ->putImage(self::PUBLIC_FILENAME, $image);
    }

    private function assertStorageAccessor_deleteImage_isCalledOnce(): void
    {
        \Phake::verify($this->storageAccessor, \Phake::times(1))
            ->deleteImage(self::PUBLIC_FILENAME);
    }

    private function createThumbnailImageWriter(): ThumbnailImageWriter
    {
        return new ThumbnailImageWriter($this->keyParser, $this->storageAccessor);
    }
}
