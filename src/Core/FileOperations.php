<?php

namespace Strider2038\ImgCache\Core;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Exception\FileOperationException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class FileOperations
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

    public function setLogger(LoggerInterface $logger)
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

        if ($contents === false) {
            throw new FileOperationException("Cannot read file '{$filename}'");
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

}
