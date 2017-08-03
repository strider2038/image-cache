<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support\Phake;

use Strider2038\ImgCache\Core\FileOperations;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
trait FileOperationsTrait
{
    public function givenFileOperations(): FileOperations
    {
        return \Phake::mock(FileOperations::class);
    }

    public function givenFileOperations_FileExists_Returns(
        FileOperations $fileOperations,
        string $filename,
        bool $value
    ): void {
        \Phake::when($fileOperations)->fileExists($filename)->thenReturn($value);
    }

    public function givenFileOperations_GetFileContents_Returns(
        FileOperations $fileOperations,
        string $filename,
        string $blob
    ): void {
        \Phake::when($fileOperations)->getFileContents($filename)->thenReturn($blob);
    }

    public function assertFileOperations_CopyFileTo_IsCalledOnce(
        FileOperations $fileOperations,
        string $source,
        string $destination
    ): void {
        \Phake::verify($fileOperations, \Phake::times(1))->copyFileTo($source, $destination);
    }

    public function assertFileOperations_CreateFile_IsCalledOnce(
        FileOperations $fileOperations,
        string $filename,
        string $data
    ): void {
        \Phake::verify($fileOperations, \Phake::times(1))->createFile($filename, $data);
    }
}