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
    Component, Request, RequestInterface, Route, RouterInterface
};
use Strider2038\ImgCache\Exception\{
    InvalidRouteException, RequestException
};
use Strider2038\ImgCache\Imaging\Image\ImageFile;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Router extends Component implements RouterInterface 
{
    
    protected static $methodsToActions = [
        Request::METHOD_GET    => 'get',
        Request::METHOD_POST   => 'create',
        Request::METHOD_PUT    => 'replace',
        Request::METHOD_PATCH  => 'rebuild',
        Request::METHOD_DELETE => 'delete',
    ];

    protected static $allowedExtensions = [
        ImageFile::EXTENSION_JPG,
        ImageFile::EXTENSION_JPEG,
        ImageFile::EXTENSION_PNG,
    ];
    
    public function getRoute(RequestInterface $request): Route 
    {
        $requestMethod = $request->getMethod();
        
        if (!array_key_exists($requestMethod, self::$methodsToActions)) {
            throw new InvalidRouteException('Route not found');
        }
        
        $url = $request->getUrl(PHP_URL_PATH);
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        if (!in_array($ext, self::$allowedExtensions)) {
            throw new RequestException('Requested file has incorrect extension');
        }
        
        $app = $this->getApp();
        
        return new Route(
            new ImageController($app->security, $app->imgcache),
            self::$methodsToActions[$requestMethod]
        );
    }
    
}
