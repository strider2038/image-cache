<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ProjectTestCase extends TestCase
{
    protected function givenImageFactoryCreatesImageFile(
        ImageFactoryInterface $imageFactory,
        string $filename
    ): ImageFile {
        $image = \Phake::mock(ImageFile::class);

        \Phake::when($imageFactory)->createImageFile($filename)->thenReturn($image);

        return $image;
    }

    protected function assertImageFactoryCreateImageFileIsCalled(
        ImageFactoryInterface $imageFactory,
        string $filename
    ): void {
        \Phake::verify($imageFactory, \Phake::times(1))
            ->createImageFile($filename);
    }

    protected function assertImageSavedTo(ImageInterface $extractedImage, string $destination): void
    {
        \Phake::verify($extractedImage, \Phake::times(1))
            ->saveTo($destination);
    }
}