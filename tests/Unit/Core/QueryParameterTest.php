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
use Strider2038\ImgCache\Core\QueryParameter;

class QueryParameterTest extends TestCase
{
    private const NAME = 'name';
    private const VALUE = 'value';

    /** @test */
    public function construct_givenNameAndValue_nameAndValueAreSet(): void
    {
        $parameter = new QueryParameter(self::NAME, self::VALUE);

        $this->assertEquals(self::NAME, $parameter->getName());
        $this->assertEquals(self::VALUE, $parameter->getValue());
    }
}
