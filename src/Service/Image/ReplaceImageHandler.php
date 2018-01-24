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
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactoryInterface;

/**
 * Handles PUT request for creating new resource or replacing old one. If resource is already
 * exists it will be replaced with all thumbnails deleted. Response with 201 (created)
 * code is returned.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ReplaceImageHandler implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ImageFilenameFactoryInterface */
    private $filenameFactory;

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageCacheInterface */
    private $imageCache;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ImageFilenameFactoryInterface $filenameFactory,
        ImageStorageInterface $imageStorage,
        ImageCacheInterface $imageCache,
        ImageFactoryInterface $imageFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->filenameFactory = $filenameFactory;
        $this->imageStorage = $imageStorage;
        $this->imageCache = $imageCache;
        $this->imageFactory = $imageFactory;
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        $filename = $this->filenameFactory->createImageFilenameFromRequest($request);

        if ($this->imageStorage->imageExists($filename)) {
            $this->imageStorage->deleteImage($filename);
            $fileNameMask = $this->imageStorage->getImageFileNameMask($filename);
            $this->imageCache->deleteImagesByMask($fileNameMask);
        }

        $stream = $request->getBody();
        $image = $this->imageFactory->createImageFromStream($stream);
        $this->imageStorage->putImage($filename, $image);

        return $this->responseFactory->createMessageResponse(
            new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
            sprintf('Image "%s" successfully put to storage.', $filename)
        );
    }
}
