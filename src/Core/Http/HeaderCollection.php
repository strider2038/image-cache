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

use Strider2038\ImgCache\Collection\AbstractClassCollection;
use Strider2038\ImgCache\Collection\StringList;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class HeaderCollection extends AbstractClassCollection
{
    protected function getElementClassName(): string
    {
        return StringList::class;
    }

    /** {@inheritDoc} */
    public function get($key): StringList
    {
        return parent::get($key) ?? new StringList();
    }

    /**
     * @inheritdoc
     * @throws \DomainException
     */
    public function add($element): bool
    {
        throw new \DomainException('Not implemented');
    }
}
