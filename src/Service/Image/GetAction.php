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

use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;

/**
 * Handles GET request for resource. Returns response with status code 201 and image body
 * if resource is found and response with status code 404 when not found.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GetAction extends AbstractImageAction
{
    public function run(): ResponseInterface
    {
        $image = $this->imageCache->get($this->location);

        if ($image === null) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::NOT_FOUND)
            );
        } else {
            $response = $this->responseFactory->createFileResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED),
                $image->getFilename()
            );
        }

        return $response;
    }
}
