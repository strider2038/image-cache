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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationsCollection implements \IteratorAggregate, \Countable
{
    /** @var TransformationInterface[] */
    private $collection = [];
    
    public function add(TransformationInterface $transformation): void
    {
        $this->collection[] = $transformation;
    }
    
    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->collection);
    }
    
    public function count(): int
    {
        return count($this->collection);
    }
}
