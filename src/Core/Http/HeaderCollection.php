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

use Strider2038\ImgCache\Core\IterableCollection;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class HeaderCollection extends IterableCollection
{
    /** @param HeaderValueCollection[] $elements */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $key => $value) {
            $this->set(new HttpHeaderEnum($key), $value);
        }
    }

    public function set(HttpHeaderEnum $name, HeaderValueCollection $values): void
    {
        $this->elements[$name->getValue()] = $values;
    }

    public function get(HttpHeaderEnum $name): HeaderValueCollection
    {
        return $this->elements[$name->getValue()] ?? new HeaderValueCollection();
    }

    public function containsKey(HttpHeaderEnum $name): bool
    {
        return array_key_exists($name->getValue(), $this->elements);
    }
}
