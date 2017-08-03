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

use Strider2038\ImgCache\Core\FileOperations;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class FileOperationsTest extends FileTestCase
{

    public function testIsFile_GivenFileExist_TrueIsReturned(): void
    {
        $filename = $this->givenFile();
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isFile($filename);

        $this->assertTrue($exist);
    }

    public function testIsFile_GivenFileNotExists_FalseIsReturned(): void
    {
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isFile(self::FILENAME_NOT_EXIST);

        $this->assertFalse($exist);
    }

    public function testIsFile_GivenDirectory_FalseIsReturned(): void
    {
        $directory = $this->givenDirectory();
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isFile($directory);

        $this->assertFalse($exist);
    }

    public function testIsDirectory_GivenFileExist_FalseIsReturned(): void
    {
        $filename = $this->givenFile();
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isDirectory($filename);

        $this->assertFalse($exist);
    }

    public function testIsDirectory_GivenFileNotExists_FalseIsReturned(): void
    {
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isDirectory(self::FILENAME_NOT_EXIST);

        $this->assertFalse($exist);
    }

    public function testIsDirectory_GivenDirectory_TrueIsReturned(): void
    {
        $directory = $this->givenDirectory();
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isDirectory($directory);

        $this->assertTrue($exist);
    }

    private function createFileOperations(): FileOperations
    {
        $fileOperations = new FileOperations();

        return $fileOperations;
    }
}
