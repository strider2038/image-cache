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
use Strider2038\ImgCache\Core\HeaderValueCollection;

class HeaderValueCollectionTest extends TestCase
{
    /** @test */
    public function construct_givenOneValue_collectionCountIs1(): void
    {
        $values = ['v'];

        $collection = new HeaderValueCollection($values);

        $this->assertCount(1, $collection);
    }
}
