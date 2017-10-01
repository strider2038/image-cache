<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;

class StringListTest extends TestCase
{
    private const STRING_VALUE = 'a';

    /** @test */
    public function construct_givenString_stringInCollection(): void
    {
        $list = new StringList([self::STRING_VALUE]);

        $this->assertContains(self::STRING_VALUE, $list->toArray());
    }
}
