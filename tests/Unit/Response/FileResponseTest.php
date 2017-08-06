<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Response;

use Strider2038\ImgCache\Response\FileResponse;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class FileResponseTest extends FileTestCase
{

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found/
     */
    public function testConstruct_GivenFileDoesNotExist_ExceptionThrown(): void
    {
        new FileResponse(self::FILENAME_NOT_EXIST);
    }

    /**
     * @runInSeparateProcess
     * @group separate
     */
    public function testSend_GivenFile_ContentIsEchoed(): void
    {
        $filename = $this->givenFile();
        $response = new FileResponse($filename);
        $this->expectOutputString(file_get_contents($filename));

        $response->send();

        $this->assertEquals(200, $response->getHttpCode());
    }
}
