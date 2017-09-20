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
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\SecurityInterface;
use Strider2038\ImgCache\Enum\HttpStatusCode;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;

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
        ResponseFactoryInterface $responseFactory,
        SecurityInterface $security,
        ImageCacheInterface $imageCache,
        RequestInterface $request
    ) {
        parent::__construct($responseFactory, $security);
        $this->imageCache = $imageCache;
        $this->request = $request;
    }
    
    protected function getSafeActions(): array
    {
        return ['get'];
    }

    /**
     * Handles GET request for resource. Returns response with status code 201 and image body
     * if resource is found and response with status code 404 when not found.
     * @param string $location
     * @return ResponseInterface
     */
    public function actionGet(string $location): ResponseInterface
    {
        /** @var ImageFile $image */
        $image = $this->imageCache->get($location);

        if ($image === null) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCode(HttpStatusCode::NOT_FOUND)
            );
        } else {
            $response = $this->responseFactory->createFileResponse(
                new HttpStatusCode(HttpStatusCode::CREATED),
                $image->getFilename()
            );
        }

        return $response;
    }

    /**
     * Handles POST request for creating resource. If resource already exists then response with
     * status code 409 (conflict) will be returned, otherwise with 201 (created) code.
     * @param string $location
     * @return ResponseInterface
     */
    public function actionCreate(string $location): ResponseInterface
    {
        if ($this->imageCache->exists($location)) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCode(HttpStatusCode::CONFLICT),
                sprintf(
                    "File '%s' already exists in cache source. Use PUT method to replace file there.",
                    $location
                )
            );
        } else {
            $this->imageCache->put($location, $this->request->getBody());

            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCode(HttpStatusCode::CREATED),
                sprintf("File '%s' successfully created in cache", $location)
            );
        }

        return $response;
    }

    /**
     * Handles PUT request for creating new resource or replacing old one. If resource is already
     * exists it will be replaced with all thumbnails deleted. Response with 201 (created)
     * code is returned.
     * @param string $location
     * @return ResponseInterface
     */
    public function actionReplace(string $location): ResponseInterface
    {
        if ($this->imageCache->exists($location)) {
            $this->imageCache->delete($location);
        }

        $this->imageCache->put($location, $this->request->getBody());

        return $this->responseFactory->createMessageResponse(
            new HttpStatusCode(HttpStatusCode::CREATED),
            sprintf("File '%s' successfully created in cache", $location)
        );
    }

    /**
     * Handles DELETE request for deleting resource from cache source and all it's cached
     * thumbnails. If resource does not exist response with 404 code will be returned, otherwise
     * response with 200 code.
     * @param string $location
     * @return ResponseInterface
     */
    public function actionDelete(string $location): ResponseInterface
    {
        if (!$this->imageCache->exists($location)) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCode(HttpStatusCode::NOT_FOUND),
                sprintf("File '%s' does not exist", $location)
            );
        } else {
            $this->imageCache->delete($location);

            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCode(HttpStatusCode::OK),
                sprintf(
                    "File '%s' was successfully deleted from"
                    . " cache source and from cache with all thumbnails",
                    $location
                )
            );
        }

        return $response;
    }

    /**
     * Handles PATCH request for rebuilding cached resource with all its thumbnails
     * @param string $location
     * @return ResponseInterface
     */
    public function actionRebuild(string $location): ResponseInterface
    {
        if (!$this->imageCache->exists($location)) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCode(HttpStatusCode::NOT_FOUND),
                sprintf("File '%s' does not exist", $location)
            );
        } else {
            $this->imageCache->rebuild($location);

            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCode(HttpStatusCode::OK),
                sprintf(
                    "File '%s' was successfully rebuilt with all its thumbnails",
                    $location
                )
            );
        }

        return $response;
    }
}
