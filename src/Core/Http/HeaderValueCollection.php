<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core\Http;

use Strider2038\ImgCache\Collection\IterableCollection;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class HeaderValueCollection extends IterableCollection
{
    /** @param string[] $elements */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $value) {
            $this->add($value);
        }
    }

    public function add(string $value): void
    {
        $this->elements[] = $value;
    }
}
