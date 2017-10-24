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

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\StreamInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
trait FileOperationsTrait
{
    protected function givenFileOperations(): FileOperationsInterface
    {
        return \Phake::mock(FileOperationsInterface::class);
    }

    protected function givenFileOperations_isFile_returns(
        FileOperationsInterface $fileOperations,
        string $filename,
        bool $value
    ): void {
        \Phake::when($fileOperations)->isFile($filename)->thenReturn($value);
    }

    protected function givenFileOperations_isDirectory_returns(
        FileOperationsInterface $fileOperations,
        string $directory,
        bool $value
    ): void {
        \Phake::when($fileOperations)->isDirectory($directory)->thenReturn($value);
    }

    protected function givenFileOperations_getFileContents_returns(
        FileOperationsInterface $fileOperations,
        string $filename,
        string $blob
    ): void {
        \Phake::when($fileOperations)->getFileContents($filename)->thenReturn($blob);
    }

    protected function givenFileOperations_openFile_returnsStream(
        FileOperationsInterface $fileOperations,
        string $filename,
        string $mode
    ): StreamInterface {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($fileOperations)->openFile($filename, $mode)->thenReturn($stream);

        return $stream;
    }

    protected function assertFileOperations_copyFileTo_isCalledOnce(
        FileOperationsInterface $fileOperations,
        string $source,
        string $destination
    ): void {
        \Phake::verify($fileOperations, \Phake::times(1))->copyFileTo($source, $destination);
    }

    protected function assertFileOperations_createFile_isCalledOnce(
        FileOperationsInterface $fileOperations,
        string $filename,
        string $data
    ): void {
        \Phake::verify($fileOperations, \Phake::times(1))->createFile($filename, $data);
    }

    protected function assertFileOperations_deleteFile_isCalledOnce(
        FileOperationsInterface $fileOperations,
        string $filename
    ): void {
        \Phake::verify($fileOperations, \Phake::times(1))->deleteFile($filename);
    }

    protected function assertFileOperations_createDirectory_isCalledOnce(
        FileOperationsInterface $fileOperations,
        string $directory
    ): void {
        \Phake::verify($fileOperations, \Phake::times(1))->createDirectory($directory);
    }
}
