<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration\ImageSource;

use Strider2038\ImgCache\Collection\AbstractClassCollection;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageSourceCollection extends AbstractClassCollection
{
    public function __construct(array $elements = [])
    {
        parent::__construct($elements, AbstractImageSource::class);
    }
}
