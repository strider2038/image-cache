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
class HeaderCollection extends IterableCollection
{
    /** @param HeaderValueCollection[] $elements */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $headerName => $values) {
            $this->set($headerName, $values);
        }
    }

    public function set(string $name, HeaderValueCollection $values): void
    {
        $this->elements[$name] = $values;
    }

    public function get(string $name): HeaderValueCollection
    {
        return $this->elements[$name] ?? new HeaderValueCollection();
    }

    public function containsKey(string $name): bool
    {
        return array_key_exists($name, $this->elements);
    }
}
