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
    /** @var string */
    protected $location;

    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    /** @var ImageStorageInterface */
    protected $imageStorage;

    /** @var ImageCacheInterface */
    protected $imageCache;

    public function __construct(
        string $location,
        ResponseFactoryInterface $responseFactory,
        ImageStorageInterface $imageStorage,
        ImageCacheInterface $imageCache
    ) {
        $this->location = $location;
        $this->responseFactory = $responseFactory;
        $this->imageStorage = $imageStorage;
        $this->imageCache = $imageCache;
    }

    public function run(): ResponseInterface
    {
        $storedImage = $this->imageStorage->find($this->location);

        if ($storedImage === null) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::NOT_FOUND)
            );
        } else {
            $this->imageCache->put($this->location, $storedImage);
            $cachedImage = $this->imageCache->get($this->location);

            $response = $this->responseFactory->createFileResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
                $cachedImage->getFilename()
            );
        }

        return $response;
    }
}
