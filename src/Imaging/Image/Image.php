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
class Image
{
    /** @var SaveOptions */
    private $saveOptions;

    /** @var StreamInterface */
    private $data;

    public function __construct(SaveOptions $saveOptions, StreamInterface $data)
    {
        $this->saveOptions = $saveOptions;
        $this->data = $data;
    }

    public function getSaveOptions(): SaveOptions
    {
        return $this->saveOptions;
    }

    public function getData(): StreamInterface
    {
        return $this->data;
    }
}
