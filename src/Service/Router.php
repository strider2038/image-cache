<?php

namespace Strider2038\ImgCache\Service;

use Strider2038\ImgCache\Core\{
    Component, 
    RouterInterface,
    Request,
    Route
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Router extends Component implements RouterInterface 
{
    
    public function getRoute(Request $request): Route 
    {
        return new Route(
            new ImageController(),
            'get',
            []
        );
    }
    
}
