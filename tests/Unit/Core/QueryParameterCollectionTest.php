<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Core\QueryParameterInterface;

class QueryParameterCollectionTest extends TestCase
{
    private const NAME = 'name';
    private const VALUE = 'value';

    /** @test */
    public function toArray_givenQueryParameter_properlyFormattedArrayReturned(): void
    {
        $parameter = $this->givenQueryParameter();
        $collection = new QueryParameterCollection([$parameter]);

        $array = $collection->toArray();

        $this->assertEquals([self::NAME => self::VALUE], $array);
    }

    private function givenQueryParameter(): QueryParameterInterface
    {
        $parameter = \Phake::mock(QueryParameterInterface::class);
        \Phake::when($parameter)->getName()->thenReturn(self::NAME);
        \Phake::when($parameter)->getValue()->thenReturn(self::VALUE);

        return $parameter;
    }
}
