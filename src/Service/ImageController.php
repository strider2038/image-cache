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
    Controller, RequestInterface, ResponseInterface, SecurityInterface
};
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Response\{
    ConflictResponse, CreatedResponse, ImageResponse, NotFoundResponse, SuccessResponse
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageController extends Controller 
{
    /** @var ImageCacheInterface */
    private $imageCache;

    public function __construct(SecurityInterface $security, ImageCacheInterface $imageCache)
    {
        parent::__construct($security);
        $this->imageCache = $imageCache;
    }
    
    protected function getSafeActions(): array
    {
        return ['get'];
    }

    /**
     * Handles GET request for resource. Returns ImageResponse if resource is found and
     * NotFoundResponse when not found.
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function actionGet(RequestInterface $request): ResponseInterface
    {
        $filename = $request->getUrl(PHP_URL_PATH);

        /** @var ImageFile $image */
        $image = $this->imageCache->get($filename);
        if ($image === null) {
            return new NotFoundResponse();
        }
        
        return new ImageResponse($image->getFilename());
    }

    /**
     * Handles POST request for creating resource. If resource already exists then ConflictResponse
     * will be returned, otherwise - CreatedResponse.
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function actionCreate(RequestInterface $request): ResponseInterface
    {
        $filename = $request->getUrl(PHP_URL_PATH);

        if ($this->imageCache->exists($filename)) {
            return new ConflictResponse(
                "File '{$filename}' already exists in cache source. "
                . "Use PUT method to replace file in it."
            );
        }

        $this->imageCache->put($filename, $request->getBody());

        return new CreatedResponse("File '{$filename}' successfully created in cache");
    }

    /**
     * Handles PUT request for creating new resource or replacing old one. If resource is already
     * exists it will be replaced with all thumbnails deleted. CreatedResponse is returned.
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function actionReplace(RequestInterface $request): ResponseInterface
    {
        $filename = $request->getUrl(PHP_URL_PATH);

        if ($this->imageCache->exists($filename)) {
            $this->imageCache->delete($filename);
        }

        $this->imageCache->put($filename, $request->getBody());

        return new CreatedResponse("File '{$filename}' successfully created in cache");
    }

    /**
     * Handles DELETE request for deleting resource from cache source and all it's cached
     * thumbnails
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function actionDelete(RequestInterface $request): ResponseInterface
    {
        $filename = $request->getUrl(PHP_URL_PATH);

        if (!$this->imageCache->exists($filename)) {
            return new NotFoundResponse("File {$filename} does not exist");
        }

        $this->imageCache->delete($filename);
        return new SuccessResponse(
            "File {$filename} was successfully deleted from"
            . " cache source and from cache with all thumbnails"
        );
    }

    /**
     * Handles PATCH request for rebuilding cached resource with all its thumbnails
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function actionRebuild(RequestInterface $request): ResponseInterface
    {
        $filename = $request->getUrl(PHP_URL_PATH);

        if (!$this->imageCache->exists($filename)) {
            return new NotFoundResponse("File {$filename} does not exist");
        }

        $this->imageCache->rebuild($filename);
        return new SuccessResponse(
            "File {$filename} was successfully rebuilt with all its thumbnails"
        );
    }
}
