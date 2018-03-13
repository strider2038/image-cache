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
 * Handles GET request for resource. Returns response with status code 201 and image body
 * if resource is found and response with status code 404 when not found.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GetImageHandler implements RequestHandlerInterface
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

        $storedImage = $this->imageStorage->getImage($filename);

        $this->imageCache->putImage($filename, $storedImage);
        $cachedImage = $this->imageCache->getImage($filename);

        $response = $this->responseFactory->createFileResponse(
            new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
            $cachedImage->getFilename()
        );

        return $response;
    }
}
