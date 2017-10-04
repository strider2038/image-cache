<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Transformation;

use Strider2038\ImgCache\Collection\IterableCollection;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationsCollection extends IterableCollection
{
    public function add(TransformationInterface $transformation): void
    {
        $this->elements[] = $transformation;
    }
}
