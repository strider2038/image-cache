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

use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceCollection;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\ImageCacheFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageStorageFactoryInterface;
use Strider2038\ImgCache\Imaging\Naming\DirectoryName;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ClearAllRequestHandler implements RequestHandlerInterface
{
    /** @var ImageSourceCollection */
    private $imageSourceCollection;
    /** @var ImageStorageFactoryInterface */
    private $imageStorageFactory;
    /** @var ImageCacheFactoryInterface */
    private $imageCacheFactory;
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    public function __construct(
        ImageSourceCollection $imageSourceCollection,
        ImageStorageFactoryInterface $imageStorageFactory,
        ImageCacheFactoryInterface $imageCacheFactory,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->imageSourceCollection = $imageSourceCollection;
        $this->imageStorageFactory = $imageStorageFactory;
        $this->imageCacheFactory = $imageCacheFactory;
        $this->responseFactory = $responseFactory;
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        /** @var AbstractImageSource $imageSource */
        foreach ($this->imageSourceCollection as $imageSource) {
            $this->clearAllStorageImages($imageSource);
            $this->clearAllCacheImages($imageSource);
        }

        return $this->createOkResponse();
    }

    private function createOkResponse(): ResponseInterface
    {
        return $this->responseFactory->createMessageResponse(
            new HttpStatusCodeEnum(HttpStatusCodeEnum::OK),
            'All images deleted from all storages and caches.'
        );
    }

    private function clearAllStorageImages(AbstractImageSource $imageSource): void
    {
        $imageStorage = $this->imageStorageFactory->createImageStorageForImageSource($imageSource);
        $imageStorage->deleteDirectoryContents(new DirectoryName('/'));
    }

    private function clearAllCacheImages(AbstractImageSource $imageSource): void
    {
        $cacheDirectory = $imageSource->getCacheDirectory();
        $imageCache = $this->imageCacheFactory->createImageCacheForWebDirectory($cacheDirectory);
        $imageCache->deleteDirectoryContents('/');
    }
}
