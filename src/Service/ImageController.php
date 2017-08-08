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
    /** @var RequestInterface */
    private $request;

    /** @var ImageCacheInterface */
    private $imageCache;

    public function __construct(
        SecurityInterface $security,
        ImageCacheInterface $imageCache,
        RequestInterface $request
    ) {
        parent::__construct($security);
        $this->imageCache = $imageCache;
        $this->request = $request;
    }
    
    protected function getSafeActions(): array
    {
        return ['get'];
    }

    /**
     * Handles GET request for resource. Returns ImageResponse if resource is found and
     * NotFoundResponse when not found.
     * @param string $location
     * @return \Strider2038\ImgCache\Core\ResponseInterface
     */
    public function actionGet(string $location): ResponseInterface
    {
        /** @var ImageFile $image */
        $image = $this->imageCache->get($location);
        if ($image === null) {
            return new NotFoundResponse();
        }
        
        return new ImageResponse($image->getFilename());
    }

    /**
     * Handles POST request for creating resource. If resource already exists then ConflictResponse
     * will be returned, otherwise - CreatedResponse.
     * @param string $location
     * @return \Strider2038\ImgCache\Core\ResponseInterface
     */
    public function actionCreate(string $location): ResponseInterface
    {
        if ($this->imageCache->exists($location)) {
            return new ConflictResponse(
                "File '{$location}' already exists in cache source. "
                . "Use PUT method to replace file in it."
            );
        }

        $this->imageCache->put($location, $this->request->getBody());

        return new CreatedResponse("File '{$location}' successfully created in cache");
    }

    /**
     * Handles PUT request for creating new resource or replacing old one. If resource is already
     * exists it will be replaced with all thumbnails deleted. CreatedResponse is returned.
     * @param string $location
     * @return \Strider2038\ImgCache\Core\ResponseInterface
     */
    public function actionReplace(string $location): ResponseInterface
    {
        if ($this->imageCache->exists($location)) {
            $this->imageCache->delete($location);
        }

        $this->imageCache->put($location, $this->request->getBody());

        return new CreatedResponse("File '{$location}' successfully created in cache");
    }

    /**
     * Handles DELETE request for deleting resource from cache source and all it's cached
     * thumbnails
     * @param string $location
     * @return \Strider2038\ImgCache\Core\ResponseInterface
     */
    public function actionDelete(string $location): ResponseInterface
    {
        if (!$this->imageCache->exists($location)) {
            return new NotFoundResponse("File {$location} does not exist");
        }

        $this->imageCache->delete($location);
        return new SuccessResponse(
            "File {$location} was successfully deleted from"
            . " cache source and from cache with all thumbnails"
        );
    }

    /**
     * Handles PATCH request for rebuilding cached resource with all its thumbnails
     * @param string $location
     * @return \Strider2038\ImgCache\Core\ResponseInterface
     */
    public function actionRebuild(string $location): ResponseInterface
    {
        if (!$this->imageCache->exists($location)) {
            return new NotFoundResponse("File {$location} does not exist");
        }

        $this->imageCache->rebuild($location);
        return new SuccessResponse(
            "File {$location} was successfully rebuilt with all its thumbnails"
        );
    }
}
