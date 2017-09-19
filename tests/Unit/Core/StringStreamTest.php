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
use Strider2038\ImgCache\Core\StringStream;

class StringStreamTest extends TestCase
{
    private const CONTENTS = 'contents';

    /** @test */
    public function getContents_givenString_stringContentsIsReturned(): void
    {
        $stream = new StringStream(self::CONTENTS);

        $contents = $stream->getContents();

        $this->assertEquals(self::CONTENTS, $contents);
    }

    /** @test */
    public function toString_givenString_stringContentsIsReturned(): void
    {
        $stream = new StringStream(self::CONTENTS);

        $contents = $stream . '';

        $this->assertEquals(self::CONTENTS, $contents);
    }
}
