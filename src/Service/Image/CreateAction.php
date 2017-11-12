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
    /** @var string */
    protected $location;

    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    /** @var ImageStorageInterface */
    protected $imageStorage;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var RequestInterface */
    private $request;

    public function __construct(
        string $location,
        ResponseFactoryInterface $responseFactory,
        ImageStorageInterface $imageStorage,
        ImageFactoryInterface $imageFactory,
        RequestInterface $request
    ) {
        $this->location = $location;
        $this->responseFactory = $responseFactory;
        $this->imageStorage = $imageStorage;
        $this->imageFactory = $imageFactory;
        $this->request = $request;
    }

    public function run(): ResponseInterface
    {
        if ($this->imageStorage->exists($this->location)) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::CONFLICT),
                sprintf(
                    'File "%s" already exists in image source. Use PUT method to replace image in storage.',
                    $this->location
                )
            );
        } else {
            $stream = $this->request->getBody();
            $image = $this->imageFactory->createFromStream($stream);
            $this->imageStorage->put($this->location, $image);

            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
                sprintf('File "%s" was successfully put to storage', $this->location)
            );
        }

        return $response;
    }
}
