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
 * Handles DELETE request for deleting resource from cache source and all it's cached
 * thumbnails. If resource does not exist response with 404 code will be returned, otherwise
 * response with 200 code.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class DeleteAction implements ActionInterface
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

        if ($this->imageStorage->imageExists($location)) {
            $this->imageStorage->deleteImage($location);
            $fileNameMask = $this->imageStorage->getImageFileNameMask($location);
            $this->imageCache->deleteImagesByMask($fileNameMask);

            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::OK),
                sprintf(
                    'File "%s" was successfully deleted from'
                    . ' image storage and all thumbnails were deleted from cache.',
                    $location
                )
            );
        } else {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::NOT_FOUND),
                sprintf('File "%s" does not exist.', $location)
            );
        }

        return $response;
    }
}
