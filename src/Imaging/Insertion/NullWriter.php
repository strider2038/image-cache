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
    public function imageExists(string $key): bool
    {
        throw new NotAllowedException('Method is not allowed');
    }

    public function insertImage(string $key, Image $image): void
    {
        throw new NotAllowedException('Method is not allowed');
    }

    public function deleteImage(string $key): void
    {
        throw new NotAllowedException('Method is not allowed');
    }

    public function getImageFileNameMask(string $key): string
    {
        throw new NotAllowedException('Method is not allowed');
    }
}
