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

use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Core\{
    Controller,
    RequestInterface,
    ResponseInterface,
    SecurityInterface
};
use Strider2038\ImgCache\Response\{
    ImageResponse,
    ErrorResponse,
    NotFoundResponse
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageController extends Controller 
{
    /** @var ImageCacheInterface */
    private $imgcache;

    public function __construct(SecurityInterface $security, ImageCacheInterface $imgcache)
    {
        parent::__construct($security);
        $this->imgcache = $imgcache;
    }
    
    protected function getSafeActions(): array
    {
        return ['get'];
    }
    
    public function actionGet(RequestInterface $request): ResponseInterface
    {
        $filename = $request->getUrl(PHP_URL_PATH);
        
        $image = $this->imgcache->get($filename);
        if ($image === null) {
            return new NotFoundResponse();
        }
        
        return new ImageResponse($image->getFilename());
    }
    
    public function actionCreate(RequestInterface $request): ResponseInterface
    {
        return new NotFoundResponse();
    }
    
    public function actionReplace(RequestInterface $request): ResponseInterface
    {
        return new NotFoundResponse();
    }
    
    public function actionRefresh(RequestInterface $request): ResponseInterface
    {
        return new NotFoundResponse();
    }
    
    public function actionDelete(RequestInterface $request): ResponseInterface
    {
        return new NotFoundResponse();
    }
}
