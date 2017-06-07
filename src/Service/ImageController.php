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

use Strider2038\ImgCache\Core\Controller;
use Strider2038\ImgCache\Core\Request;
use Strider2038\ImgCache\Response\NotFoundResponse;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageController extends Controller 
{
    
    public function actionGet(Request $request) 
    {
        return new NotFoundResponse();
    }
    
    public function actionCreate(Request $request) 
    {
        return new NotFoundResponse();
    }
    
    public function actionReplace(Request $request) 
    {
        return new NotFoundResponse();
    }
    
    public function actionRefresh(Request $request) 
    {
        return new NotFoundResponse();
    }
    
    public function actionDelete(Request $request) 
    {
        return new NotFoundResponse();
    }
}
