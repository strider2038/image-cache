<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging;

use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageCacheInterface
{
    public function getImage(ImageFilenameInterface $filename): ImageFile;
    public function putImage(ImageFilenameInterface $filename, Image $image): void;
    public function deleteImagesByMask(string $fileNameMask): void;
    public function deleteDirectoryContents(string $directory): void;
}
