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

use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\FileOperations;
use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class FileOperationsTest extends FileTestCase
{
    use LoggerTrait;

    const COPY_FILENAME_SOURCE = '/file_source.dat';
    const COPY_FILENAME_DESTINATION = '/file_destination.dat';
    const CREATE_FILENAME = self::TEST_CACHE_DIR . '/fs_create.dat';
    const CREATE_FILENAME_CONTENTS = 'create_filename';
    const CREATE_DIRECTORY_NAME = '/tmp/test';
    const EMPTY_FILENAME = self::TEST_CACHE_DIR . '/empty.dat';

    /** @var Filesystem */
    private $filesystem;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp()
    {
        parent::setUp();
        $this->filesystem = \Phake::mock(Filesystem::class);
        $this->logger = $this->givenLogger();
    }

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

    public function testCopyFileTo_GivenSourceAndDestination_FilesystemCopyIsCalled(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->copyFileTo(self::COPY_FILENAME_SOURCE, self::COPY_FILENAME_DESTINATION);

        \Phake::verify($this->filesystem, \Phake::times(1))
            ->copy(self::COPY_FILENAME_SOURCE, self::COPY_FILENAME_DESTINATION);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Cannot copy file
     */
    public function testCopyFileTo_GivenInvalidSourceOrDestination_ExceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();
        \Phake::when($this->filesystem)
            ->copy(\Phake::anyParameters())
            ->thenThrow(new IOException(''));

        $fileOperations->copyFileTo(self::COPY_FILENAME_SOURCE, self::COPY_FILENAME_DESTINATION);
    }

    public function testGetFileContents_GivenExistingFile_FileContentsIsReturned(): void
    {
        $filename = $this->givenFile();
        $fileOperations = $this->createFileOperations();

        $contents = $fileOperations->getFileContents($filename);

        $this->assertEquals(file_get_contents($filename), $contents);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Cannot read file
     */
    public function testGetFileContents_GivenFileNotExists_ExceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->getFileContents('/not.exist');
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp  /File .* is empty/
     */
    public function testGetFileContents_GivenFileIsEmpty_ExceptionThrown(): void
    {
        file_put_contents(self::EMPTY_FILENAME, '');
        $fileOperations = $this->createFileOperations();

        $fileOperations->getFileContents(self::EMPTY_FILENAME);
    }

    public function testCreateFile_GivenFilenameAndContents_FilesystemDumpFileIsCalled(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->createFile(self::CREATE_FILENAME, self::CREATE_FILENAME_CONTENTS);

        \Phake::verify($this->filesystem, \Phake::times(1))
            ->dumpFile(self::CREATE_FILENAME, self::CREATE_FILENAME_CONTENTS);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Cannot create file
     */
    public function testCreateFile_GivenInvalidFilenameAndContents_ExceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();
        \Phake::when($this->filesystem)->dumpFile(\Phake::anyParameters())->thenThrow(new IOException(''));

        $fileOperations->createFile(self::CREATE_FILENAME, self::CREATE_FILENAME_CONTENTS);
    }

    public function testDeleteFile_GivenFileExists_FilesystemRemoveIsCalled(): void
    {
        $fileOperations = $this->createFileOperations();
        $filename = $this->givenFile();

        $fileOperations->deleteFile($filename);

        \Phake::verify($this->filesystem, \Phake::times(1))->remove($filename);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Cannot delete file .* it does not exist/
     */
    public function testDeleteFile_GivenFileDoesNotExist_ExceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->deleteFile('/not.exist');
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Cannot delete file .* because of unexpected error/
     */
    public function testDeleteFile_GivenFileExistsAndCannotBeDeleted_ExceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();
        $filename = $this->givenFile();
        \Phake::when($this->filesystem)->remove(\Phake::anyParameters())->thenThrow(new IOException(('')));

        $fileOperations->deleteFile($filename);
    }

    public function testCreateDirectory_GivenDirectoryName_FilesystemMkdirIsCalled(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->createDirectory(self::CREATE_DIRECTORY_NAME);

        \Phake::verify($this->filesystem, \Phake::times(1))->mkdir(self::CREATE_DIRECTORY_NAME, 0775);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Cannot create directory
     */
    public function testCreateDirectory_GivenDirectoryNameCannotBeCreated_ExceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();
        \Phake::when($this->filesystem)->mkdir(\Phake::anyParameters())->thenThrow(new IOException(''));

        $fileOperations->createDirectory(self::CREATE_DIRECTORY_NAME);
    }

    private function createFileOperations(): FileOperations
    {
        $fileOperations = new FileOperations($this->filesystem);
        $fileOperations->setLogger($this->logger);

        return $fileOperations;
    }
}
