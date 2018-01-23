<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service;

use Strider2038\ImgCache\Core\Controller;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 * @deprecated
 */
class ImageController extends Controller 
{
    protected function getSafeActionIds(): array
    {
        return ['get'];
    }
}
