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
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageStorageInterface
{
    public function getImage(ImageFilenameInterface $filename): Image;
    public function putImage(ImageFilenameInterface $filename, Image $image): void;
    public function imageExists(ImageFilenameInterface $filename): bool;
    public function deleteImage(ImageFilenameInterface $filename): void;
    public function getImageFileNameMask(ImageFilenameInterface $filename): string;
}
