<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service\Routing;

use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\Uri;
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Exception\InvalidRouteException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class UrlRouteDetector implements UrlRouteDetectorInterface
{
    /** @var RoutingPathCollection */
    private $routingMap;

    public function __construct(RoutingPathCollection $routingMap)
    {
        if (count($routingMap) === 0) {
            throw new InvalidConfigurationException('Routing map cannot be empty');
        }
        $this->routingMap = $routingMap;
    }

    public function getUrlRoute(RequestInterface $request): UrlRoute
    {
        $requestUrl = $request->getUri()->getPath();

        foreach ($this->routingMap as $path) {
            /** @var RoutingPath $path */
            $prefix = str_replace('/', '\\/', $path->getUrlPrefix());
            $pattern = '/^' . $prefix . '\/(?<url>.*)$/i';
            if (
                preg_match_all($pattern, $requestUrl, $matches)
                && array_key_exists('url', $matches)
                && count($matches['url']) > 0
            ) {
                $uri = new Uri('/' . $matches['url'][0]);

                return new UrlRoute($path->getControllerId(), $uri);
            }
        }

        throw new InvalidRouteException('Route not found');
    }
}
