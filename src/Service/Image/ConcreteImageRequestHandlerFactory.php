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

use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ConcreteImageRequestHandlerFactory
{
    /** @var ImageStorageInterface */
    private $imageStorage;
    /** @var ImageCacheInterface */
    private $imageCache;
    /** @var ResponseFactoryInterface */
    private $responseFactory;
    /** @var ImageFilenameFactoryInterface */
    private $filenameFactory;
    /** @var ImageFactoryInterface */
    private $imageFactory;

    public function __construct(
        ImageStorageInterface $imageStorage,
        ImageCacheInterface $imageCache,
        ResponseFactoryInterface $responseFactory,
        ImageFilenameFactoryInterface $filenameFactory,
        ImageFactoryInterface $imageFactory
    ) {
        $this->imageStorage = $imageStorage;
        $this->imageCache = $imageCache;
        $this->responseFactory = $responseFactory;
        $this->filenameFactory = $filenameFactory;
        $this->imageFactory = $imageFactory;
    }

    public function createGetImageHandler(): GetImageHandler
    {
        return new GetImageHandler(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageCache
        );
    }

    public function createCreateImageHandler(): CreateImageHandler
    {
        return new CreateImageHandler(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageFactory
        );
    }

    public function createReplaceImageHandler(): ReplaceImageHandler
    {
        return new ReplaceImageHandler(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageCache,
            $this->imageFactory
        );
    }

    public function createDeleteImageHandler(): DeleteImageHandler
    {
        return new DeleteImageHandler(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageCache
        );
    }
}
