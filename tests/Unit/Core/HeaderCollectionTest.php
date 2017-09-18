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
use Strider2038\ImgCache\Core\HeaderCollection;
use Strider2038\ImgCache\Core\HeaderValueCollection;
use Strider2038\ImgCache\Enum\HttpHeader;

class HeaderCollectionTest extends TestCase
{
    /** @test */
    public function construct_givenArrayWithHeader_headerWithValuesIsSet(): void
    {
        $elements = $this->givenElements();

        $collection = new HeaderCollection($elements);

        $values = $collection->get(new HttpHeader(HttpHeader::AUTHORIZATION));
        $this->assertInstanceOf(HeaderValueCollection::class, $values);
    }

    /** @test */
    public function containsKey_givenArrayWithHeader_returnsTrue(): void
    {
        $elements = $this->givenElements();
        $collection = new HeaderCollection($elements);

        $result = $collection->containsKey(new HttpHeader(HttpHeader::AUTHORIZATION));

        $this->assertTrue($result);
    }

    private function givenElements(): array
    {
        $elements = [
            HttpHeader::AUTHORIZATION => new HeaderValueCollection([
                'AuthorizationValue'
            ])
        ];

        return $elements;
    }
}
