<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Insertion;

use Strider2038\ImgCache\Exception\NotAllowedException;
use Strider2038\ImgCache\Imaging\Image\Image;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class NullWriter implements ImageWriterInterface
{
    public function imageExists(string $filename): bool
    {
        throw new NotAllowedException('Method is not allowed');
    }

    public function insertImage(string $filename, Image $image): void
    {
        throw new NotAllowedException('Method is not allowed');
    }

    public function deleteImage(string $filename): void
    {
        throw new NotAllowedException('Method is not allowed');
    }

    public function deleteDirectoryContents(string $directory): void
    {
        throw new NotAllowedException('Method is not allowed');
    }

    public function getImageFileNameMask(string $filename): string
    {
        throw new NotAllowedException('Method is not allowed');
    }
}
