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

use Strider2038\ImgCache\Enum\HttpHeader;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class HeaderCollection extends IterableCollection
{
    /** @param HeaderValueCollection[] $elements */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $key => $value) {
            $this->set(new HttpHeader($key), $value);
        }
    }

    public function set(HttpHeader $name, HeaderValueCollection $values): void
    {
        $this->elements[$name->getValue()] = $values;
    }

    public function get(HttpHeader $name): ? HeaderValueCollection
    {
        return $this->elements[$name->getValue()] ?? null;
    }

    public function containsKey(HttpHeader $name): bool
    {
        return array_key_exists($name->getValue(), $this->elements);
    }
}
