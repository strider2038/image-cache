<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing\Imagick;

use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickTransformerFactory implements ImageTransformerFactoryInterface
{
    public function createTransformer(StreamInterface $stream): ImageTransformerInterface
    {
        $imagick = new \Imagick();
        $imagick->readImageBlob($stream->getContents());

        return new ImagickTransformer($imagick);
    }
}
