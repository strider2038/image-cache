<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Naming;

use Strider2038\ImgCache\Core\Http\RequestInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageFilenameFactoryInterface
{
    public function createImageFilenameFromRequest(RequestInterface $request): ImageFilenameInterface;
}
