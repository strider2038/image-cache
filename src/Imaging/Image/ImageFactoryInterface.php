<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Image;

use Strider2038\ImgCache\Core\StreamInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageFactoryInterface
{
    public function createFromFile(string $filename): Image;
    public function createFromStream(StreamInterface $stream): Image;
    /** @deprecated */
    public function createImageFile(string $filename): ImageFile;
    /** @deprecated */
    public function createImageBlob(string $blob): ImageBlob;
}
