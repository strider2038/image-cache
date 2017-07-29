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

use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\Component;
use Strider2038\ImgCache\Core\Request;
use Strider2038\ImgCache\Core\RequestInterface;
use Strider2038\ImgCache\Core\Route;
use Strider2038\ImgCache\Core\RouterInterface;
use Strider2038\ImgCache\Exception\InvalidRouteException;
use Strider2038\ImgCache\Exception\RequestException;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Router extends Component implements RouterInterface 
{
    /** @var ImageValidatorInterface */
    private $imageValidator;

    protected $methodsToActions = [
        Request::METHOD_GET    => 'get',
        Request::METHOD_POST   => 'create',
        Request::METHOD_PUT    => 'replace',
        Request::METHOD_PATCH  => 'rebuild',
        Request::METHOD_DELETE => 'delete',
    ];

    public function __construct(Application $app, ImageValidatorInterface $imageValidator)
    {
        parent::__construct($app);
        $this->imageValidator = $imageValidator;
    }

    public function getRoute(RequestInterface $request): Route 
    {
        $requestMethod = $request->getMethod();
        
        if (!array_key_exists($requestMethod, $this->methodsToActions)) {
            throw new InvalidRouteException('Route not found');
        }
        
        $url = $request->getUrl(PHP_URL_PATH);
        if (!$this->imageValidator->hasValidImageExtension($url)) {
            throw new RequestException('Requested file has incorrect extension');
        }
        
        $app = $this->getApp();
        
        return new Route(
            new ImageController($app->security, $app->imgcache),
            $this->methodsToActions[$requestMethod]
        );
    }
}
