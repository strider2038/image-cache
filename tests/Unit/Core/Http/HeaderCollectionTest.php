<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core\Http;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\Http\HeaderCollection;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;

class HeaderCollectionTest extends TestCase
{
    /** @test */
    public function construct_givenArrayWithHeader_headerWithValuesIsSet(): void
    {
        $elements = $this->givenElements();

        $collection = new HeaderCollection($elements);

        $values = $collection->get(HttpHeaderEnum::AUTHORIZATION);
        $this->assertInstanceOf(StringList::class, $values);
    }

    /** @test */
    public function get_givenKeyAndNoElements_emptyHeaderValueCollectionReturned(): void
    {
        $collection = new HeaderCollection();

        $values = $collection->get(HttpHeaderEnum::AUTHORIZATION);

        $this->assertInstanceOf(StringList::class, $values);
        $this->assertCount(0, $values);
    }

    /**
     * @test
     * @expectedException \DomainException
     * @expectedExceptionMessage Not implemented
     */
    public function add_givenElement_domainExceptionThrown(): void
    {
        $collection = new HeaderCollection();

        $collection->add(new StringList());
    }

    private function givenElements(): array
    {
        $elements = [
            HttpHeaderEnum::AUTHORIZATION => new StringList([
                'AuthorizationValue'
            ])
        ];

        return $elements;
    }
}
