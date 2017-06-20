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
    Controller,
    Request,
    ResponseInterface
};
use Strider2038\ImgCache\Response\{
    ErrorResponse,
    NotFoundResponse
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageController extends Controller 
{
    protected function getInsecureActions(): array
    {
        return ['get'];
    }
    
    public function actionGet(Request $request): ResponseInterface
    {
        $filename = $request->getUrl(PHP_URL_PATH);
        return new NotFoundResponse();
    }
    
    public function actionCreate(Request $request): ResponseInterface
    {
        return new NotFoundResponse();
    }
    
    public function actionReplace(Request $request): ResponseInterface
    {
        return new NotFoundResponse();
    }
    
    public function actionRefresh(Request $request): ResponseInterface
    {
        return new NotFoundResponse();
    }
    
    public function actionDelete(Request $request): ResponseInterface
    {
        return new NotFoundResponse();
    }
}
