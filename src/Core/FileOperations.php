<?php

namespace Strider2038\ImgCache\Core;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Exception\FileOperationException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class FileOperations implements FileOperationsInterface
{
    /** @var Filesystem */
    private $filesystem;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(Filesystem $filesystem, StreamFactoryInterface $streamFactory)
    {
        $this->filesystem = $filesystem;
        $this->streamFactory = $streamFactory;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function isFile(string $filename): bool
    {
        return file_exists($filename) && !is_dir($filename);
    }

    public function isDirectory(string $directory): bool
    {
        return is_dir($directory);
    }

    public function findByMask(string $filenameMask): StringList
    {
        $list = glob($filenameMask);

        return new StringList($list);
    }

    public function copyFileTo(string $source, string $destination): void
    {
        try {
            $this->filesystem->copy($source, $destination);

            $this->logger->info(sprintf('File copied from "%s" to "%s"', $source, $destination));
        } catch (IOException $exception) {
            throw new FileOperationException(
                sprintf('Cannot copy file from "%s" to "%s"', $source, $destination),
                $exception
            );
        }
    }

    public function getFileContents(string $filename): string
    {
        try {
            $contents = file_get_contents($filename);
        } catch (\Exception $exception) {
            throw new FileOperationException(sprintf('Cannot read file "%s"', $filename), $exception);
        }

        if (empty($contents)) {
            throw new FileOperationException(sprintf('File "%s" is empty', $filename));
        }

        $this->logger->info(sprintf('Contents of file "%s" was read', $filename));

        return $contents;
    }

    public function createFile(string $filename, string $data): void
    {
        try {
            $this->filesystem->dumpFile($filename, $data);

            $this->logger->info(sprintf('File "%s" was created', $filename));
        } catch (IOException $exception) {
            throw new FileOperationException(sprintf('Cannot create file "%s"', $filename), $exception);
        }
    }

    public function deleteFile(string $filename): void
    {
        if (!$this->isFile($filename)) {
            throw new FileOperationException(sprintf('Cannot delete file "%s": it does not exist', $filename));
        }

        try {
            $this->filesystem->remove($filename);

            $this->logger->info(sprintf('File "%s" was deleted', $filename));
        } catch (IOException $exception) {
            throw new FileOperationException(
                sprintf('Cannot delete file "%s" because of unexpected error', $filename),
                $exception
            );
        }
    }

    public function createDirectory(string $directory, int $mode = 0775): void
    {
        try {
            $this->filesystem->mkdir($directory, $mode);

            $this->logger->info(sprintf(
                'Directory "%s" was created recursively with mode %s',
                $directory,
                decoct($mode)
            ));
        } catch (IOException $exception) {
            throw new FileOperationException(sprintf('Cannot create directory "%s"', $directory), $exception);
        }
    }

    public function openFile(string $filename, ResourceStreamModeEnum $mode): StreamInterface
    {
        if (!$this->isFile($filename)) {
            throw new FileOperationException(sprintf(
                'Invalid file "%s" or file does not exist',
                $filename
            ));
        }

        try {
            $stream = $this->streamFactory->createStreamByParameters($filename, $mode);

            $this->logger->info(sprintf(
                'File "%s" is successfully opened in mode "%s"',
                $filename,
                $mode
            ));

            return $stream;
        } catch (\Exception $exception) {
            throw new FileOperationException(
                sprintf('Cannot open file "%s" in mode "%s"', $filename, $mode),
                $exception
            );
        }
    }
}
