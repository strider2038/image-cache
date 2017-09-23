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

use Strider2038\ImgCache\Core\ReadOnlyResourceStream;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ReadOnlyResourceStreamTest extends FileTestCase
{
    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function write_givenJsonFile_exceptionThrown(): void
    {
        $stream = new ReadOnlyResourceStream($this->givenFile());

        $stream->write('');
    }
}
