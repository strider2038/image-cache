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

use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Exception\InvalidImageException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageFactoryInterface
{
    /**
     * @param StreamInterface $stream
     * @param ImageParameters|null $parameters
     * @return Image
     * @throws InvalidImageException
     */
    public function createImageFromStream(StreamInterface $stream, ImageParameters $parameters = null): Image;
}
