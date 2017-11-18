<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service;

use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\Uri;
use Strider2038\ImgCache\Service\Routing\UrlRoute;
use Strider2038\ImgCache\Service\Routing\UrlRouteDetectorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageDefaultRouteDetector implements UrlRouteDetectorInterface
{
    private const CONTROLLER_ID = 'imageController';

    public function getUrlRoute(RequestInterface $request): UrlRoute
    {
        $path = $request->getUri()->getPath();

        return new UrlRoute(self::CONTROLLER_ID, new Uri($path));
    }
}
