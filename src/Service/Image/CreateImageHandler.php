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

use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Exception\InvalidImageException;
use Strider2038\ImgCache\Exception\InvalidRequestException;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactoryInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameInterface;

/**
 * Handles POST request for creating resource. If resource already exists then response with
 * status code 409 (conflict) will be returned, otherwise with 201 (created) code.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class CreateImageHandler implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;
    /** @var ImageFilenameFactoryInterface */
    private $filenameFactory;
    /** @var ImageStorageInterface */
    private $imageStorage;
    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var ImageFilenameInterface */
    private $filename;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ImageFilenameFactoryInterface $filenameFactory,
        ImageStorageInterface $imageStorage,
        ImageFactoryInterface $imageFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->filenameFactory = $filenameFactory;
        $this->imageStorage = $imageStorage;
        $this->imageFactory = $imageFactory;
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        $this->filename = $this->filenameFactory->createImageFilenameFromRequest($request);

        if ($this->imageStorage->imageExists($this->filename)) {
            $response = $this->createConflictResponse();
        } else {
            $stream = $request->getBody();
            $this->putImageWithFilenameToStorage($stream);
            $response = $this->createCreatedResponse();
        }

        return $response;
    }

    private function createConflictResponse(): ResponseInterface
    {
        return $this->responseFactory->createMessageResponse(
            new HttpStatusCodeEnum(HttpStatusCodeEnum::CONFLICT),
            sprintf(
                'File "%s" already exists in image storage. Use PUT method to replace it.',
                $this->filename
            )
        );
    }

    private function createCreatedResponse(): ResponseInterface
    {
        return $this->responseFactory->createMessageResponse(
            new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
            sprintf('File "%s" was successfully put to storage.', $this->filename)
        );
    }

    private function putImageWithFilenameToStorage(StreamInterface $stream): void
    {
        try {
            $image = $this->imageFactory->createImageFromStream($stream);
        } catch (InvalidImageException $exception) {
            throw new InvalidRequestException($exception->getMessage());
        }

        $this->imageStorage->putImage($this->filename, $image);
    }
}
