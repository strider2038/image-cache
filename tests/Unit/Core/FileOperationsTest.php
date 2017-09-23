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
use Strider2038\ImgCache\Core\ResourceStream;
use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class FileOperationsTest extends FileTestCase
{
    use LoggerTrait;

    private const COPY_FILENAME_SOURCE = '/file_source.dat';
    private const COPY_FILENAME_DESTINATION = '/file_destination.dat';
    private const CREATE_FILENAME = self::TEST_CACHE_DIR . '/fs_create.dat';
    private const CREATE_FILENAME_CONTENTS = 'create_filename';
    private const CREATE_DIRECTORY_NAME = '/tmp/test';
    private const EMPTY_FILENAME = self::TEST_CACHE_DIR . '/empty.dat';

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

    /** @test */
    public function isFile_givenFileExist_trueIsReturned(): void
    {
        $filename = $this->givenFile();
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isFile($filename);

        $this->assertTrue($exist);
    }

    /** @test */
    public function isFile_givenFileNotExists_falseIsReturned(): void
    {
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isFile(self::FILENAME_NOT_EXIST);

        $this->assertFalse($exist);
    }

    /** @test */
    public function isFile_givenDirectory_falseIsReturned(): void
    {
        $directory = $this->givenDirectory();
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isFile($directory);

        $this->assertFalse($exist);
    }

    /** @test */
    public function isDirectory_givenFileExist_falseIsReturned(): void
    {
        $filename = $this->givenFile();
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isDirectory($filename);

        $this->assertFalse($exist);
    }

    /** @test */
    public function isDirectory_givenFileNotExists_falseIsReturned(): void
    {
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isDirectory(self::FILENAME_NOT_EXIST);

        $this->assertFalse($exist);
    }

    /** @test */
    public function isDirectory_givenDirectory_trueIsReturned(): void
    {
        $directory = $this->givenDirectory();
        $fileOperations = $this->createFileOperations();

        $exist = $fileOperations->isDirectory($directory);

        $this->assertTrue($exist);
    }

    /** @test */
    public function copyFileTo_givenSourceAndDestination_filesystemCopyIsCalled(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->copyFileTo(self::COPY_FILENAME_SOURCE, self::COPY_FILENAME_DESTINATION);

        \Phake::verify($this->filesystem, \Phake::times(1))
            ->copy(self::COPY_FILENAME_SOURCE, self::COPY_FILENAME_DESTINATION);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Cannot copy file
     */
    public function copyFileTo_givenInvalidSourceOrDestination_exceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();
        \Phake::when($this->filesystem)
            ->copy(\Phake::anyParameters())
            ->thenThrow(new IOException(''));

        $fileOperations->copyFileTo(self::COPY_FILENAME_SOURCE, self::COPY_FILENAME_DESTINATION);
    }

    /** @test */
    public function getFileContents_givenExistingFile_fileContentsIsReturned(): void
    {
        $filename = $this->givenFile();
        $fileOperations = $this->createFileOperations();

        $contents = $fileOperations->getFileContents($filename);

        $this->assertEquals(file_get_contents($filename), $contents);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Cannot read file
     */
    public function getFileContents_givenFileNotExists_exceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->getFileContents('/not.exist');
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp  /File .* is empty/
     */
    public function getFileContents_givenFileIsEmpty_exceptionThrown(): void
    {
        file_put_contents(self::EMPTY_FILENAME, '');
        $fileOperations = $this->createFileOperations();

        $fileOperations->getFileContents(self::EMPTY_FILENAME);
    }

    /** @test */
    public function createFile_givenFilenameAndContents_filesystemDumpFileIsCalled(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->createFile(self::CREATE_FILENAME, self::CREATE_FILENAME_CONTENTS);

        \Phake::verify($this->filesystem, \Phake::times(1))
            ->dumpFile(self::CREATE_FILENAME, self::CREATE_FILENAME_CONTENTS);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Cannot create file
     */
    public function ceateFile_givenInvalidFilenameAndContents_exceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();
        \Phake::when($this->filesystem)->dumpFile(\Phake::anyParameters())->thenThrow(new IOException(''));

        $fileOperations->createFile(self::CREATE_FILENAME, self::CREATE_FILENAME_CONTENTS);
    }

    /** @test */
    public function deleteFile_givenFileExists_filesystemRemoveIsCalled(): void
    {
        $fileOperations = $this->createFileOperations();
        $filename = $this->givenFile();

        $fileOperations->deleteFile($filename);

        \Phake::verify($this->filesystem, \Phake::times(1))->remove($filename);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Cannot delete file .* it does not exist/
     */
    public function deleteFile_givenFileDoesNotExist_exceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->deleteFile('/not.exist');
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Cannot delete file .* because of unexpected error/
     */
    public function deleteFile_givenFileExistsAndCannotBeDeleted_exceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();
        $filename = $this->givenFile();
        \Phake::when($this->filesystem)->remove(\Phake::anyParameters())->thenThrow(new IOException(('')));

        $fileOperations->deleteFile($filename);
    }

    /** @test */
    public function createDirectory_givenDirectoryName_filesystemMkdirIsCalled(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->createDirectory(self::CREATE_DIRECTORY_NAME);

        \Phake::verify($this->filesystem, \Phake::times(1))->mkdir(self::CREATE_DIRECTORY_NAME, 0775);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Cannot create directory
     */
    public function createDirectory_givenDirectoryNameCannotBeCreated_exceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();
        \Phake::when($this->filesystem)->mkdir(\Phake::anyParameters())->thenThrow(new IOException(''));

        $fileOperations->createDirectory(self::CREATE_DIRECTORY_NAME);
    }

    /** @test */
    public function openFile_givenFileAndReadOnlyMode_streamIsReturned(): void
    {
        $fileOperations = $this->createFileOperations();
        $filename = $this->givenFile();

        $stream = $fileOperations->openFile($filename, ResourceStream::MODE_READ_ONLY);

        $this->assertInstanceOf(ResourceStream::class, $stream);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Cannot open file
     */
    public function openFile_givenNotExistingFileAndReadOnlyMode_exceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->openFile(self::FILENAME_NOT_EXIST, ResourceStream::MODE_READ_ONLY);
    }

    private function createFileOperations(): FileOperations
    {
        $fileOperations = new FileOperations($this->filesystem);
        $fileOperations->setLogger($this->logger);

        return $fileOperations;
    }
}
