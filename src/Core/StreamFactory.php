<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class StreamFactory implements StreamFactoryInterface
{
    public function createStreamByParameters(string $descriptor, ResourceStreamModeEnum $mode): StreamInterface
    {
        $resource = fopen($descriptor, $mode);

        return new ResourceStream($resource);
    }

    public function createStreamFromData(string $data): StreamInterface
    {
        return new StringStream($data);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new ResourceStream($resource);
    }
}
