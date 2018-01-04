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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Image
{
    /** @var StreamInterface */
    private $data;

    /** @var ImageParameters */
    private $parameters;

    public function __construct(StreamInterface $data, ImageParameters $parameters)
    {
        $this->data = $data;
        $this->parameters = $parameters;
    }

    public function getData(): StreamInterface
    {
        return $this->data;
    }

    public function setParameters(ImageParameters $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): ImageParameters
    {
        return $this->parameters;
    }
}
