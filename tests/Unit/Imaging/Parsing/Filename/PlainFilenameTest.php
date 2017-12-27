<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Filename;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilename;

class PlainFilenameTest extends TestCase
{
    private const VALUE = 'a.jpg';

    /** @test */
    public function construct_givenValue_valueSet(): void
    {
        $key = new PlainFilename(self::VALUE);

        $this->assertEquals(self::VALUE, $key->getValue());
    }
}
