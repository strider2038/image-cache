<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Collection;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class StringList extends IterableCollection
{
    public function __construct(array $stringList = [])
    {
        foreach ($stringList as $value) {
            $this->add($value);
        }
    }

    public function add(string $value): void
    {
        $this->elements[] = $value;
    }

    public function process(\Closure $closure): void
    {
        foreach ($this->elements as $key => $value) {
            $this->elements[$key] = $closure->call($this, $value);
        }
    }
}
