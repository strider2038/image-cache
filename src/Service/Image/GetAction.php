<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service\Image;

use Strider2038\ImgCache\Core\ActionInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;

/**
 * Handles GET request for resource. Returns response with status code 201 and image body
 * if resource is found and response with status code 404 when not found.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GetAction implements ActionInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageCacheInterface */
    private $imageCache;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ImageStorageInterface $imageStorage,
        ImageCacheInterface $imageCache
    ) {
        $this->responseFactory = $responseFactory;
        $this->imageStorage = $imageStorage;
        $this->imageCache = $imageCache;
    }

    public function processRequest(RequestInterface $request): ResponseInterface
    {
        $location = $request->getUri()->getPath();

        $storedImage = $this->imageStorage->find($location);

        if ($storedImage === null) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::NOT_FOUND)
            );
        } else {
            $this->imageCache->put($location, $storedImage);
            $cachedImage = $this->imageCache->get($location);

            $response = $this->responseFactory->createFileResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
                $cachedImage->getFilename()
            );
        }

        return $response;
    }
}
