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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class StringList extends ArrayCollection
{
    public function __construct(array $elements = [])
    {
        foreach ($elements as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param string $element
     * @return bool
     */
    public function add($element): bool
    {
        return parent::add((string) $element);
    }

    public function set($key, $value): void
    {
        parent::set($key, (string) $value);
    }

    public function process(\Closure $closure): void
    {
        foreach ($this as $key => $value) {
            $this[$key] = $closure->call($this, $value);
        }
    }

    public function implode(string $glue = ','): string
    {
        return implode($glue, $this->toArray());
    }
}
