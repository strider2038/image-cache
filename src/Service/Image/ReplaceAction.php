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
    /** @var string */
    protected $location;

    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    /** @var ImageStorageInterface */
    protected $imageStorage;

    /** @var ImageCacheInterface */
    protected $imageCache;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var RequestInterface */
    private $request;

    public function __construct(
        string $location,
        ResponseFactoryInterface $responseFactory,
        ImageStorageInterface $imageStorage,
        ImageCacheInterface $imageCache,
        ImageFactoryInterface $imageFactory,
        RequestInterface $request
    ) {
        $this->location = $location;
        $this->responseFactory = $responseFactory;
        $this->imageStorage = $imageStorage;
        $this->imageCache = $imageCache;
        $this->imageFactory = $imageFactory;
        $this->request = $request;
    }

    public function run(): ResponseInterface
    {
        if ($this->imageStorage->exists($this->location)) {
            $this->imageStorage->delete($this->location);
            $fileNameMask = $this->imageStorage->getFileNameMask($this->location);
            $this->imageCache->deleteByMask($fileNameMask);
        }

        $stream = $this->request->getBody();
        $image = $this->imageFactory->createFromStream($stream);
        $this->imageStorage->put($this->location, $image);

        return $this->responseFactory->createMessageResponse(
            new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
            sprintf('Image "%s" successfully put to storage.', $this->location)
        );
    }
}
