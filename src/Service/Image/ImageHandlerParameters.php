<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service\Image;

use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Enum\HttpMethodEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageHandlerParameters
{
    /** @var HttpMethodEnum */
    private $httpMethod;

    /** @var AbstractImageSource */
    private $imageSource;

    public function __construct(HttpMethodEnum $httpMethod, AbstractImageSource $imageSource)
    {
        $this->httpMethod = $httpMethod;
        $this->imageSource = $imageSource;
    }

    public function getHttpMethod(): HttpMethodEnum
    {
        return $this->httpMethod;
    }

    public function getImageSource(): AbstractImageSource
    {
        return $this->imageSource;
    }
}
