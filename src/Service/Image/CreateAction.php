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
use Strider2038\ImgCache\Imaging\ImageCacheInterface;

/**
 * Handles POST request for creating resource. If resource already exists then response with
 * status code 409 (conflict) will be returned, otherwise with 201 (created) code.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class CreateAction extends AbstractImageAction
{
    /** @var RequestInterface */
    private $request;

    public function __construct(
        string $location,
        ImageCacheInterface $imageCache,
        ResponseFactoryInterface $responseFactory,
        RequestInterface $request
    ) {
        parent::__construct($location, $imageCache, $responseFactory);
        $this->request = $request;
    }

    public function run(): ResponseInterface
    {
        if ($this->imageCache->exists($this->location)) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::CONFLICT),
                sprintf(
                    'File "%s" already exists in cache source. Use PUT method to replace file there.',
                    $this->location
                )
            );
        } else {
            $this->imageCache->put($this->location, $this->request->getBody());

            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
                sprintf('File "%s" successfully created in cache', $this->location)
            );
        }

        return $response;
    }
}
