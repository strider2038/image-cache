<?php

namespace Strider2038\ImgCache\Core;

/**
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface RouterInterface 
{
    public function getRoute(Request $request): Route;
}
