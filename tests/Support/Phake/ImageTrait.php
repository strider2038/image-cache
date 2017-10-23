<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support\Phake;

use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;

/**
 * @deprecated
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
trait ImageTrait
{
    protected function givenImage(): ImageInterface
    {
        $image = \Phake::mock(ImageInterface::class);

        return $image;
    }

    protected function givenProcessingImage(): ProcessingImageInterface
    {
        $image = \Phake::mock(ProcessingImageInterface::class);

        return $image;
    }

    protected function givenImageFactory_CreateImageFile_ReturnsImage(
        ImageFactoryInterface $imageFactory,
        string $filename
    ): ImageFile {
        $image = \Phake::mock(ImageFile::class);

        \Phake::when($imageFactory)->createImageFile($filename)->thenReturn($image);

        return $image;
    }

    protected function assertImageFactory_CreateImageFile_IsCalledOnce(
        ImageFactoryInterface $imageFactory,
        string $filename
    ): void {
        \Phake::verify($imageFactory, \Phake::times(1))
            ->createImageFile($filename);
    }

    protected function assertImage_SavedTo_IsCalledOnce(ImageInterface $extractedImage, string $destination): void
    {
        \Phake::verify($extractedImage, \Phake::times(1))
            ->saveTo($destination);
    }
}