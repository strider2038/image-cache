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

use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactoryInterface;

/**
 * Handles DELETE request for deleting resource from cache source and all it's cached
 * thumbnails. If resource does not exist response with 404 code will be returned, otherwise
 * response with 200 code.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class DeleteImageHandler implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ImageFilenameFactoryInterface */
    private $filenameFactory;

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageCacheInterface */
    private $imageCache;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ImageFilenameFactoryInterface $filenameFactory,
        ImageStorageInterface $imageStorage,
        ImageCacheInterface $imageCache
    ) {
        $this->responseFactory = $responseFactory;
        $this->filenameFactory = $filenameFactory;
        $this->imageStorage = $imageStorage;
        $this->imageCache = $imageCache;
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        $filename = $this->filenameFactory->createImageFilenameFromRequest($request);

        if ($this->imageStorage->imageExists($filename)) {
            $this->imageStorage->deleteImage($filename);
            $fileNameMask = $this->imageStorage->getImageFileNameMask($filename);
            $this->imageCache->deleteImagesByMask($fileNameMask);

            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::OK),
                sprintf(
                    'File "%s" was successfully deleted from'
                    . ' image storage and all thumbnails were deleted from cache.',
                    $filename
                )
            );
        } else {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::NOT_FOUND),
                sprintf('File "%s" does not exist.', $filename)
            );
        }

        return $response;
    }
}
