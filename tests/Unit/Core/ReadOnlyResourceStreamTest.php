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
    /** @test */
    public function getContents_givenJsonFile_jsonFileContentsIsReturned(): void
    {
        $stream = new ReadOnlyResourceStream($this->givenFile());

        $contents = $stream->getContents();

        $this->assertEquals(self::FILE_JSON_CONTENTS, $contents);
    }

    /** @test */
    public function toString_givenJsonFile_jsonFileContentsIsReturned(): void
    {
        $stream = new ReadOnlyResourceStream($this->givenFile());

        $contents = $stream . '';

        $this->assertEquals(self::FILE_JSON_CONTENTS, $contents);
    }
}
