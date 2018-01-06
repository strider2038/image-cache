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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageParametersFactory implements ImageParametersFactoryInterface
{
    /** @var int */
    private $quality;

    public function __construct(int $quality = ImageParameters::QUALITY_VALUE_DEFAULT)
    {
        $this->quality = $quality;
    }

    public function createImageParameters(): ImageParameters
    {
        $parameters = new ImageParameters();
        $parameters->setQuality($this->quality);

        return $parameters;
    }
}
