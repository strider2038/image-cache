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

use Strider2038\ImgCache\Core\RequestHandlerInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageRequestHandlerFactoryInterface
{
    public function createRequestHandlerByHttpMethod(HttpMethodEnum $method): RequestHandlerInterface;
}
