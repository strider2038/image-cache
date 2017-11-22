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
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;

/**
 * Handles PUT request for creating new resource or replacing old one. If resource is already
 * exists it will be replaced with all thumbnails deleted. Response with 201 (created)
 * code is returned.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ReplaceAction implements ActionInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageCacheInterface */
    private $imageCache;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ImageStorageInterface $imageStorage,
        ImageCacheInterface $imageCache,
        ImageFactoryInterface $imageFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->imageStorage = $imageStorage;
        $this->imageCache = $imageCache;
        $this->imageFactory = $imageFactory;
    }

    public function processRequest(RequestInterface $request): ResponseInterface
    {
        $location = $request->getUri()->getPath();

        if ($this->imageStorage->imageExists($location)) {
            $this->imageStorage->deleteImage($location);
            $fileNameMask = $this->imageStorage->getImageFileNameMask($location);
            $this->imageCache->deleteImagesByMask($fileNameMask);
        }

        $stream = $request->getBody();
        $image = $this->imageFactory->createFromStream($stream);
        $this->imageStorage->putImage($location, $image);

        return $this->responseFactory->createMessageResponse(
            new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
            sprintf('Image "%s" successfully put to storage.', $location)
        );
    }
}
