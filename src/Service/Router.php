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

use Strider2038\ImgCache\Core\{
    Component, 
    RouterInterface,
    Request,
    RequestInterface,
    Route
};
use Strider2038\ImgCache\Exception\InvalidRouteException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Router extends Component implements RouterInterface 
{
    
    protected static $methodsToActions = [
        Request::METHOD_GET    => 'get',
        Request::METHOD_POST   => 'create',
        Request::METHOD_PUT    => 'replace',
        Request::METHOD_PATCH  => 'refresh',
        Request::METHOD_DELETE => 'delete',
    ];

    public function getRoute(RequestInterface $request): Route 
    {
        if (!array_key_exists($request->getMethod(), self::$methodsToActions)) {
            throw new InvalidRouteException('Route not found');
        }
        
        return new Route(
            new ImageController($this->getApp()),
            self::$methodsToActions[$request->getMethod()]
        );
    }
    
}
