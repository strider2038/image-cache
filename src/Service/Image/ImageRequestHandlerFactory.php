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
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Exception\InvalidRouteException;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageRequestHandlerFactory implements ImageRequestHandlerFactoryInterface
{
    private const FACTORY_METHOD_NAME_MAP = [
        HttpMethodEnum::GET => 'createGetImageHandler',
        HttpMethodEnum::POST => 'createCreateImageHandler',
        HttpMethodEnum::PUT => 'createReplaceImageHandler',
        HttpMethodEnum::DELETE => 'createDeleteImageHandler',
    ];

    /** @var ImageStorageFactoryInterface */
    private $imageStorageFactory;
    /** @var ImageCacheFactoryInterface */
    private $imageCacheFactory;
    /** @var ResponseFactoryInterface */
    private $responseFactory;
    /** @var ImageFilenameFactoryInterface */
    private $filenameFactory;
    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var AbstractImageSource */
    private $imageSource;
    /** @var ImageStorageInterface */
    private $imageStorage;
    /** @var ImageCacheInterface */
    private $imageCache;

    public function __construct(
        ImageStorageFactoryInterface $imageStorageFactory,
        ImageCacheFactoryInterface $imageCacheFactory,
        ResponseFactoryInterface $responseFactory,
        ImageFilenameFactoryInterface $filenameFactory,
        ImageFactoryInterface $imageFactory
    ) {
        $this->imageStorageFactory = $imageStorageFactory;
        $this->imageCacheFactory = $imageCacheFactory;
        $this->responseFactory = $responseFactory;
        $this->filenameFactory = $filenameFactory;
        $this->imageFactory = $imageFactory;
    }

    public function createRequestHandlerByParameters(ImageHandlerParameters $parameters): RequestHandlerInterface
    {
        $createHandlerFactoryMethod = $this->getFactoryMethodByRequestHttpMethod($parameters);

        $this->imageSource = $parameters->getImageSource();
        $this->createImageStorageForImageSource();
        $this->createImageCacheWithRootDirectoryFromImageSource();

        return $this->$createHandlerFactoryMethod();
    }

    private function createImageStorageForImageSource(): void
    {
        $this->imageStorage = $this->imageStorageFactory->createImageStorageForImageSource($this->imageSource);
    }

    private function createImageCacheWithRootDirectoryFromImageSource(): void
    {
        $cacheDirectory = $this->imageSource->getCacheDirectory();
        $this->imageCache = $this->imageCacheFactory->createImageCacheWithRootDirectory($cacheDirectory);
    }

    private function getFactoryMethodByRequestHttpMethod(ImageHandlerParameters $parameters): string
    {
        $httpMethod = $parameters->getHttpMethod()->getValue();

        if (!array_key_exists($httpMethod, self::FACTORY_METHOD_NAME_MAP)) {
            throw new InvalidRouteException(
                sprintf('Handler for http method "%s" not found.', $httpMethod)
            );
        }

        return self::FACTORY_METHOD_NAME_MAP[$httpMethod];
    }

    private function createGetImageHandler(): GetImageHandler
    {
        return new GetImageHandler(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageCache
        );
    }

    private function createCreateImageHandler(): CreateImageHandler
    {
        return new CreateImageHandler(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageFactory
        );
    }

    private function createReplaceImageHandler(): ReplaceImageHandler
    {
        return new ReplaceImageHandler(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageCache,
            $this->imageFactory
        );
    }

    private function createDeleteImageHandler(): DeleteImageHandler
    {
        return new DeleteImageHandler(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageCache
        );
    }
}
