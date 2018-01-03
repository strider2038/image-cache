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
    /** @var ImageParameters */
    private $parameters;

    /** @var StreamInterface */
    private $data;

    public function __construct(ImageParameters $parameters, StreamInterface $data)
    {
        $this->parameters = $parameters;
        $this->data = $data;
    }

    public function setParameters(ImageParameters $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): ImageParameters
    {
        return $this->parameters;
    }

    public function getData(): StreamInterface
    {
        return $this->data;
    }
}
