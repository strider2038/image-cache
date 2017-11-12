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

use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\DeprecatedImageCacheInterface;

/**
 * Handles PUT request for creating new resource or replacing old one. If resource is already
 * exists it will be replaced with all thumbnails deleted. Response with 201 (created)
 * code is returned.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ReplaceAction extends AbstractImageAction
{
    /** @var RequestInterface */
    private $request;

    public function __construct(
        string $location,
        DeprecatedImageCacheInterface $imageCache,
        ResponseFactoryInterface $responseFactory,
        RequestInterface $request
    ) {
        parent::__construct($location, $imageCache, $responseFactory);
        $this->request = $request;
    }

    public function run(): ResponseInterface
    {
        if ($this->imageCache->exists($this->location)) {
            $this->imageCache->delete($this->location);
        }

        $this->imageCache->put($this->location, $this->request->getBody());

        return $this->responseFactory->createMessageResponse(
            new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
            sprintf('File "%s" successfully created in cache', $this->location)
        );
    }
}
