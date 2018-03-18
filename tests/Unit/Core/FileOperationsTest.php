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
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
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
    private const FILE_LIST_FILENAME1 = self::TEST_CACHE_DIR . '/file.dat';
    private const FILE_LIST_FILENAME2 = self::TEST_CACHE_DIR . '/file_2.dat';
    private const FILE_LIST_MASK = self::TEST_CACHE_DIR . '/file*.dat';

    /** @var Filesystem */
    private $filesystem;
    /** @var StreamFactoryInterface */
    private $streamFactory;
    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filesystem = \Phake::mock(Filesystem::class);
        $this->streamFactory = \Phake::mock(StreamFactoryInterface::class);
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
    public function findByMask_givenFiles_filesInList(): void
    {
        $this->givenAssetFilename(self::FILE_JSON,self::FILE_LIST_FILENAME1);
        $this->givenAssetFilename(self::FILE_JSON,self::FILE_LIST_FILENAME2);
        $fileOperations = $this->createFileOperations();

        $list = $fileOperations->findByMask(self::FILE_LIST_MASK);

        $this->assertContains(self::FILE_LIST_FILENAME1, $list->toArray());
        $this->assertContains(self::FILE_LIST_FILENAME2, $list->toArray());
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
    public function createFile_givenInvalidFilenameAndContents_exceptionThrown(): void
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

        $this->assertFilesystem_remove_isCalledOnceWithName($filename);
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
        $this->givenFilesystem_remove_throwsException(new IOException(('')));

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
    public function deleteDirectory_givenDirectoryExists_filesystemRemoveIsCalled(): void
    {
        $fileOperations = $this->createFileOperations();
        $directory = $this->givenDirectory();

        $fileOperations->deleteDirectory($directory);

        $this->assertFilesystem_remove_isCalledOnceWithName($directory);
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Cannot delete directory .* it does not exist/
     */
    public function deleteDirectory_givenDirectoryDoesNotExist_exceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->deleteDirectory('/not.exist');
    }
    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Cannot delete directory .* because of unexpected error/
     */
    public function deleteDirectory_givenDirectoryExistsAndCannotBeDeleted_exceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();
        $directory = $this->givenDirectory();
        $this->givenFilesystem_remove_throwsException(new IOException(('')));

        $fileOperations->deleteDirectory($directory);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileOperationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessageRegExp /Cannot delete directory contents .* it does not exist/
     */
    public function deleteDirectoryContents_givenDirectoryDoesNotExist_exceptionThrown(): void
    {
        $fileOperations = $this->createFileOperations();

        $fileOperations->deleteDirectoryContents('/not.exist');
    }

    /** @test */
    public function deleteDirectoryContents_givenDirectoryWithContents_allContentsRemoved(): void
    {
        $directory = $this->givenDirectory();
        $filename = $this->givenFile();
        $filename2 = $directory . '/file.json';
        $this->givenAssetFilename(self::FILE_JSON, $filename2);
        $fileOperations = $this->createFileOperations(new Filesystem());

        $fileOperations->deleteDirectoryContents(self::TEST_CACHE_DIR);

        $this->assertFileNotExists($filename);
        $this->assertFileNotExists($filename2);
        $this->assertFileNotExists($directory);
        $this->assertFileExists(self::TEST_CACHE_DIR);
    }

    /** @test */
    public function openFile_givenFileAndReadOnlyMode_streamIsReturned(): void
    {
        $fileOperations = $this->createFileOperations();
        $filename = $this->givenFile();
        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::READ_ONLY);
        $expectedStream = $this->givenStreamFactory_createStreamByParameters_returnsStream();

        $stream = $fileOperations->openFile($filename, $mode);

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertStreamFactory_createStreamByParameters_isCalledOnceWithFilenameAndMode($filename, $mode);
        $this->assertSame($expectedStream, $stream);
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
        $filename = $this->givenFile();
        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::READ_ONLY);
        $this->givenStreamFactory_createStreamByParameters_throwsException();

        $fileOperations->openFile($filename, $mode);
    }

    private function createFileOperations(Filesystem $filesystem = null): FileOperations
    {
        $fileOperations = new FileOperations(
            $filesystem ?? $this->filesystem,
            $this->streamFactory
        );
        $fileOperations->setLogger($this->logger);

        return $fileOperations;
    }

    private function assertStreamFactory_createStreamByParameters_isCalledOnceWithFilenameAndMode(
        string $filename,
        ResourceStreamModeEnum $mode
    ): void {
        \Phake::verify($this->streamFactory, \Phake::times(1))->createStreamByParameters($filename, $mode);
    }

    private function givenStreamFactory_createStreamByParameters_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->streamFactory)->createStreamByParameters(\Phake::anyParameters())->thenReturn($stream);

        return $stream;
    }

    private function givenStreamFactory_createStreamByParameters_throwsException(): void
    {
        \Phake::when($this->streamFactory)
            ->createStreamByParameters(\Phake::anyParameters())
            ->thenThrow(new \Exception());
    }

    private function assertFilesystem_remove_isCalledOnceWithName(string $filename): void
    {
        \Phake::verify($this->filesystem, \Phake::times(1))
            ->remove($filename);
    }

    private function givenFilesystem_remove_throwsException(\Throwable $exception): void
    {
        \Phake::when($this->filesystem)
            ->remove(\Phake::anyParameters())
            ->thenThrow($exception);
    }
}
