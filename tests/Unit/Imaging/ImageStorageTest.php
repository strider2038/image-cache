<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Extraction\ImageExtractorInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\ImageStorage;
use Strider2038\ImgCache\Imaging\Insertion\ImageWriterInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ImageStorageTest extends TestCase
{
    use ProviderTrait;

    private const VALID_KEY = '/valid_key.jpg';
    private const INVALID_KEY = 'invalid_key.jpg';
    private const FILE_NAME_MASK = 'file_name_mask';

    /** @var ImageExtractorInterface */
    private $imageExtractor;

    /** @var ImageWriterInterface */
    private $imageWriter;

    protected function setUp(): void
    {
        $this->imageExtractor = \Phake::mock(ImageExtractorInterface::class);
        $this->imageWriter = \Phake::mock(ImageWriterInterface::class);
    }

    /** @test */
    public function getImage_givenKeyAndExtractorReturnsImage_imageReturned(): void
    {
        $storage = $this->createImageStorage();
        $extractedImage = $this->givenImageExtractor_extractImage_returnsImage();

        $image = $storage->getImage(self::VALID_KEY);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertImageExtractor_extractImage_isCalledOnceWith(self::VALID_KEY);
        $this->assertSame($extractedImage, $image);
    }

    /** @test */
    public function putImage_givenKeyAndImage_imageInsertedToSource(): void
    {
        $storage = $this->createImageStorage();
        $image = $this->givenImage();
        $data = $this->givenImage_getData_returnsStream($image);

        $storage->putImage(self::VALID_KEY, $image);

        $this->assertImage_getData_isCalledOnce($image);
        $this->assertImageWriter_insert_isCalledOnceWith(self::VALID_KEY, $data);
    }

    /**
     * @test
     * @dataProvider boolValuesProvider
     * @param bool $expectedExists
     */
    public function imageExists_givenKey_existsStatusReturned(bool $expectedExists): void
    {
        $storage = $this->createImageStorage();
        $this->givenImageWriter_exists_returns($expectedExists);

        $exists = $storage->imageExists(self::VALID_KEY);

        $this->assertEquals($expectedExists, $exists);
        $this->assertImageWriter_exists_isCalledOnceWith(self::VALID_KEY);
    }

    /** @test */
    public function deleteImage_givenKey_imageDeletedFromSource(): void
    {
        $storage = $this->createImageStorage();

        $storage->deleteImage(self::VALID_KEY);

        $this->assertImageWriter_delete_isCalledOnceWith(self::VALID_KEY);
    }

    /** @test */
    public function getImageFileNameMask_givenKey_fileNameMaskReturned(): void
    {
        $storage = $this->createImageStorage();
        $this->givenImageWriter_getFileNameMask_returnsFileNameMask();

        $mask = $storage->getImageFileNameMask(self::VALID_KEY);

        $this->assertImageWriter_getFileNameMask_isCalledOnceWith(self::VALID_KEY);
        $this->assertEquals(self::FILE_NAME_MASK, $mask);
    }

    /**
     * @test
     * @param string $method
     * @param array $parameters
     * @dataProvider methodAndParametersWithInvalidKeyProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidValueException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Key must start with slash
     */
    public function givenMethod_givenInvalidKey_exceptionThrown(string $method, array $parameters): void
    {
        $storage = $this->createImageStorage();

        \call_user_func_array([$storage, $method], $parameters);
    }

    public function methodAndParametersWithInvalidKeyProvider(): array
    {
        return [
            ['getImage', ['']],
            ['getImage', [self::INVALID_KEY]],
            ['putImage', [self::INVALID_KEY, $this->givenImage()]],
            ['deleteImage', [self::INVALID_KEY]],
            ['imageExists', [self::INVALID_KEY]],
            ['getImageFileNameMask', [self::INVALID_KEY]],
        ];
    }

    private function createImageStorage(): ImageStorage
    {
        return new ImageStorage($this->imageExtractor, $this->imageWriter);
    }

    private function assertImageExtractor_extractImage_isCalledOnceWith(string $key): void
    {
        \Phake::verify($this->imageExtractor, \Phake::times(1))->extractImage($key);
    }

    private function givenImageExtractor_extractImage_returnsImage(): Image
    {
        $extractedImage = \Phake::mock(Image::class);
        \Phake::when($this->imageExtractor)->extractImage(\Phake::anyParameters())->thenReturn($extractedImage);

        return $extractedImage;
    }

    private function givenImage_getData_returnsStream(Image $image): StreamInterface
    {
        $data = \Phake::mock(StreamInterface::class);
        \Phake::when($image)->getData()->thenReturn($data);

        return $data;
    }

    private function assertImage_getData_isCalledOnce(Image $image): void
    {
        \Phake::verify($image, \Phake::times(1))->getData();
    }

    private function assertImageWriter_insert_isCalledOnceWith(string $key, StreamInterface $data): void
    {
        \Phake::verify($this->imageWriter, \Phake::times(1))->insert($key, $data);
    }

    private function assertImageWriter_delete_isCalledOnceWith(string $key): void
    {
        \Phake::verify($this->imageWriter, \Phake::times(1))->delete($key);
    }

    private function assertImageWriter_getFileNameMask_isCalledOnceWith(string $key): void
    {
        \Phake::verify($this->imageWriter, \Phake::times(1))->getFileNameMask($key);
    }

    private function assertImageWriter_exists_isCalledOnceWith(string $key): void
    {
        \Phake::verify($this->imageWriter, \Phake::times(1))->exists($key);
    }

    private function givenImageWriter_exists_returns(bool $expectedExists): void
    {
        \Phake::when($this->imageWriter)->exists(\Phake::anyParameters())->thenReturn($expectedExists);
    }

    private function givenImageWriter_getFileNameMask_returnsFileNameMask(): void
    {
        \Phake::when($this->imageWriter)->getFileNameMask(\Phake::anyParameters())->thenReturn(self::FILE_NAME_MASK);
    }

    private function givenImage(): Image
    {
        return \Phake::mock(Image::class);
    }
}
