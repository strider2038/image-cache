<?php

namespace Strider2038\ImgCache\Core;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Exception\FileOperationException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class FileOperations implements FileOperationsInterface
{
    /** @var Filesystem */
    private $filesystem;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
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

    public function copyFileTo(string $source, string $destination): void
    {
        try {
            $this->filesystem->copy($source, $destination);
            $this->logger->info(
                "File copied from '{$source}' to '{$destination}'"
            );
        } catch (IOException $exception) {
            throw new FileOperationException("Cannot copy file from '{$source}' to '{$destination}'", $exception);
        }
    }

    public function getFileContents(string $filename): string
    {
        try {
            $contents = file_get_contents($filename);
        } catch (\Exception $exception) {
            throw new FileOperationException("Cannot read file '{$filename}'", $exception);
        }

        if (empty($contents)) {
            throw new FileOperationException("File '{$filename}' is empty");
        }

        $this->logger->info("Contents of file '{$filename}' was read");

        return $contents;
    }

    public function createFile(string $filename, string $data): void
    {
        try {
            $this->filesystem->dumpFile($filename, $data);
            $this->logger->info("File '{$filename}' was created");
        } catch (IOException $exception) {
            throw new FileOperationException("Cannot create file '{$filename}'", $exception);
        }
    }

    public function deleteFile(string $filename): void
    {
        if (!$this->isFile($filename)) {
            throw new FileOperationException("Cannot delete file '{$filename}': it does not exist");
        }
        try {
            $this->filesystem->remove($filename);
            $this->logger->info("File '{$filename}' was deleted");
        } catch (IOException $exception) {
            throw new FileOperationException(
                "Cannot delete file '{$filename}' because of unexpected error",
                $exception
            );
        }
    }

    public function createDirectory(string $directory, int $mode = 0775): void
    {
        try {
            $this->filesystem->mkdir($directory, $mode);
            $this->logger->info(sprintf(
                "Directory '%s' was created recursively with mode %s",
                $directory,
                decoct($mode)
            ));
        } catch (IOException $exception) {
            throw new FileOperationException("Cannot create directory '{$directory}'", $exception);
        }
    }

    public function openFile(string $filename, string $mode): StreamInterface
    {
        try {
            $stream = new ResourceStream($filename, $mode);
            $this->logger->info(sprintf(
                "File '%s' is succesfully opened in mode '%s'",
                $filename,
                $mode
            ));

            return $stream;
        } catch (\Exception $exception) {
            throw new FileOperationException(
                sprintf("Cannot open file '%s' in mode '%s'", $filename, $mode),
                $exception
            );
        }
    }
}
