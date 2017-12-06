<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing;

use Strider2038\ImgCache\Core\Streaming\StreamInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageTransformerFactoryInterface
{
    public function createTransformer(StreamInterface $stream): ImageTransformerInterface;
}
