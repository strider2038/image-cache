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
use Strider2038\ImgCache\Imaging\ImageStorageInterface;

/**
 * Handles POST request for creating resource. If resource already exists then response with
 * status code 409 (conflict) will be returned, otherwise with 201 (created) code.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class CreateAction implements ActionInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ImageStorageInterface $imageStorage,
        ImageFactoryInterface $imageFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->imageStorage = $imageStorage;
        $this->imageFactory = $imageFactory;
    }

    public function processRequest(RequestInterface $request): ResponseInterface
    {
        $location = $request->getUri()->getPath();

        if ($this->imageStorage->exists($location)) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::CONFLICT),
                sprintf(
                    'File "%s" already exists in image storage. Use PUT method to replace image there.',
                    $location
                )
            );
        } else {
            $stream = $request->getBody();
            $image = $this->imageFactory->createFromStream($stream);
            $this->imageStorage->put($location, $image);

            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
                sprintf('File "%s" was successfully put to storage.', $location)
            );
        }

        return $response;
    }
}
