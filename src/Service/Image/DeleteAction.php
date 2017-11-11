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
 * Handles DELETE request for deleting resource from cache source and all it's cached
 * thumbnails. If resource does not exist response with 404 code will be returned, otherwise
 * response with 200 code.
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class DeleteAction extends AbstractImageAction
{
    public function run(): ResponseInterface
    {
        if (!$this->imageCache->exists($this->location)) {
            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::NOT_FOUND),
                sprintf('File "%s" does not exist', $this->location)
            );
        } else {
            $this->imageCache->delete($this->location);

            $response = $this->responseFactory->createMessageResponse(
                new HttpStatusCodeEnum(HttpStatusCodeEnum::OK),
                sprintf(
                    'File "%s" was successfully deleted from'
                    . ' cache source and from cache with all thumbnails',
                    $this->location
                )
            );
        }

        return $response;
    }
}
