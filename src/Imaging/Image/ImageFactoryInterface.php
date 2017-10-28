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
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageFactoryInterface
{
    public function create(StreamInterface $data, SaveOptions $saveOptions): Image;
    public function createFromFile(string $filename): Image;
    public function createFromData(string $data): Image;
}
