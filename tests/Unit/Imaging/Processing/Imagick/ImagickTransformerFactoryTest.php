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
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Processing\Imagick\ImagickTransformer;
use Strider2038\ImgCache\Imaging\Processing\Imagick\ImagickTransformerFactory;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ImagickTransformerFactoryTest extends FileTestCase
{
    /** @var FileOperationsInterface */
    private $fileOperations;

    protected function setUp(): void
    {
        $this->fileOperations = \Phake::mock(FileOperationsInterface::class);
    }

    /** @test */
    public function createTransformer_givenStream_ImagickTransformerIsReturned(): void
    {
        $factory = new ImagickTransformerFactory($this->fileOperations);
        $stream = $this->givenStream();

        $transformer = $factory->createTransformer($stream);

        $this->assertInstanceOf(ImagickTransformer::class, $transformer);
    }

    private function givenStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        $imageContents = file_get_contents($this->givenAssetFile(self::IMAGE_BOX_PNG));
        \Phake::when($stream)->getContents()->thenReturn($imageContents);

        return $stream;
    }
}
