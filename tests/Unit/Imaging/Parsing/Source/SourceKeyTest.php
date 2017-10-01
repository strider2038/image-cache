<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Source;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKey;

class SourceKeyTest extends TestCase
{
    private const PUBLIC_FILENAME = 'a.jpg';

    /** @test */
    public function construct_givenPublicFilename_filenameIsSet(): void
    {
        $key = new SourceKey(self::PUBLIC_FILENAME);

        $this->assertEquals(self::PUBLIC_FILENAME, $key->getPublicFilename());
    }
}
