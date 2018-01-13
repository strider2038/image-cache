<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Imagick;

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Imaging\Processing\Imagick\ImagickTransformer;
use Strider2038\ImgCache\Imaging\Processing\Point;
use Strider2038\ImgCache\Imaging\Processing\Rectangle;
use Strider2038\ImgCache\Imaging\Processing\Size;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ImagickTransformerTest extends FileTestCase
{
    private const IMAGE_SOURCE_HEIGHT = 4;
    private const IMAGE_SOURCE_WIDTH = 4;
    private const IMAGE_FINAL_WIDTH = 8;
    private const IMAGE_FINAL_HEIGHT = 8;
    private const QUALITY = 70;
    private const IMAGE_DESTINATION_FILE = self::TEST_CACHE_DIR . '/imagick_result.jpg';

    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileOperations = \Phake::mock(FileOperationsInterface::class);
        $this->streamFactory = \Phake::mock(StreamFactoryInterface::class);
    }

    /** @test */
    public function resize_givenSize_imageResize(): void
    {
        $imagick = $this->createImagickFromAssetImage(self::IMAGE_BOX_PNG);
        $transformer = $this->createTransformer($imagick);
        $size = new Size(self::IMAGE_FINAL_WIDTH, self::IMAGE_FINAL_HEIGHT);

        $returnedTransformer = $transformer->resize($size);

        $this->assertSame($returnedTransformer, $transformer);
        $finalSize = $transformer->getSize();
        $this->assertEquals($size->getHeight(), $finalSize->getHeight());
        $this->assertEquals($size->getWidth(), $finalSize->getWidth());
    }

    /** @test */
    public function crop_givenSize_imageCropped(): void
    {
        $imagick = $this->createImagickFromAssetImage(self::IMAGE_BOX_PNG);
        $transformer = $this->createTransformer($imagick);
        $rectangle = new Rectangle(self::IMAGE_SOURCE_WIDTH - 2, self::IMAGE_SOURCE_HEIGHT - 1, 1, 1);

        $returnedTransformer = $transformer->crop($rectangle);

        $this->assertSame($returnedTransformer, $transformer);
        $size = $transformer->getSize();
        $this->assertEquals(2, $size->getWidth());
        $this->assertEquals(3, $size->getHeight());
    }

    /** @test */
    public function flip_noParameters_imageFlipped(): void
    {
        $imagick = $this->createImagickFromAssetImage(self::IMAGE_POINT_PNG);
        $transformer = $this->createTransformer($imagick);

        $returnedTransformer = $transformer->flip();

        $this->assertSame($returnedTransformer, $transformer);
        $this->assertPixelColorIsBlack($imagick, 0, 3);
    }

    /** @test */
    public function flop_noParameters_imageFlopped(): void
    {
        $imagick = $this->createImagickFromAssetImage(self::IMAGE_POINT_PNG);
        $transformer = $this->createTransformer($imagick);

        $returnedTransformer = $transformer->flop();

        $this->assertSame($returnedTransformer, $transformer);
        $this->assertPixelColorIsBlack($imagick, 3, 0);
    }

    /** @test */
    public function rotate_given90degrees_imageRotatedBy90degreesClockwise(): void
    {
        $imagick = $this->createImagickFromAssetImage(self::IMAGE_POINT_PNG);
        $transformer = $this->createTransformer($imagick);

        $returnedTransformer = $transformer->rotate(90);

        $this->assertSame($returnedTransformer, $transformer);
        $this->assertPixelColorIsBlack($imagick, 3, 0);
    }

    /** @test */
    public function shift_givenShiftPoint_imageShiftedByPointCoordinates(): void
    {
        $imagick = $this->createImagickFromAssetImage(self::IMAGE_POINT_PNG);
        $transformer = $this->createTransformer($imagick);
        $point = new Point(2, 1);

        /** @var ImagickTransformer $returnedTransformer */
        $returnedTransformer = $transformer->shift($point);

        $this->assertSame($returnedTransformer, $transformer);
        $this->assertPixelColorIsBlack($returnedTransformer->getImagick(), 2, 1);
    }

    /** @test */
    public function getData_givenImage_imageIsReturnedWithTheSameContents(): void
    {
        $imagick = $this->createImagickFromAssetImage(self::IMAGE_BOX_PNG);
        $transformer = $this->createTransformer($imagick);
        $expectedData = $imagick->getImageBlob();
        $expectedStream = $this->givenStreamFactory_createStreamFromData_returnsStream();

        $data = $transformer->getData();

        $this->assertStreamFactory_createStreamFromData_isCalledOnceWithData($expectedData);
        $this->assertSame($expectedStream, $data);
    }

    /** @test */
    public function setCompressionQuality_givenQuality_valueIsSet(): void
    {
        $imagick = $this->createImagickFromAssetImage(self::IMAGE_BOX_PNG);
        $transformer = $this->createTransformer($imagick);

        $returnedTransformer = $transformer->setCompressionQuality(self::QUALITY);

        $this->assertSame($returnedTransformer, $transformer);
        $this->assertEquals(self::QUALITY, $transformer->getImagick()->getCompressionQuality());
    }

    /** @test */
    public function writeToFile_givenFilename_fileIsCreated(): void
    {
        $imagick = $this->createImagickFromAssetImage(self::IMAGE_BOX_PNG);
        $transformer = $this->createTransformer($imagick);

        $returnedTransformer = $transformer->writeToFile(self::IMAGE_DESTINATION_FILE);

        $this->assertSame($returnedTransformer, $transformer);
        $this->assertFileOperations_createDirectory_isCalledOnce();
        $this->assertFileExists(self::IMAGE_DESTINATION_FILE);
    }

    private function createImagickFromAssetImage(string $imageFilename): \Imagick
    {
        return new \Imagick($this->givenAssetFilename($imageFilename));
    }

    private function createTransformer(\Imagick $imagick): ImagickTransformer
    {
        return new ImagickTransformer($imagick, $this->fileOperations, $this->streamFactory);
    }

    private function assertFileOperations_createDirectory_isCalledOnce(): void
    {
        \Phake::verify($this->fileOperations, \Phake::times(1))->createDirectory(self::TEST_CACHE_DIR);
    }

    private function assertStreamFactory_createStreamFromData_isCalledOnceWithData(string $data): void
    {
        \Phake::verify($this->streamFactory, \Phake::times(1))->createStreamFromData($data);
    }

    private function givenStreamFactory_createStreamFromData_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->streamFactory)->createStreamFromData(\Phake::anyParameters())->thenReturn($stream);

        return $stream;
    }

    private function assertPixelColorIsBlack(\Imagick $imagick, int $x, int $y): void
    {
        $pixel = $imagick->getImagePixelColor($x, $y);
        $color = $pixel->getColor();
        $this->assertEquals(
            [
                'r' => 0,
                'g' => 0,
                'b' => 0,
                'a' => 1,
            ],
            $color
        );
    }
}
