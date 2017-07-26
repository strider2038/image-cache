<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source;

use Strider2038\ImgCache\Core\TemporaryFilesManagerInterface;
use Strider2038\ImgCache\Exception\{
    ApplicationException
};
use Strider2038\ImgCache\Helper\FileHelper;
use Strider2038\ImgCache\Imaging\Image;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileSource extends AbstractFileSource
{
    /** @var string */
    private $baseDirectory;
    
    public function __construct(
        TemporaryFilesManagerInterface $temporaryFilesManager, 
        string $baseDirectory
    ) {
        parent::__construct($temporaryFilesManager);
        
        if (substr($baseDirectory, -1) === '/') {
            $baseDirectory = substr($baseDirectory, 0, -1);
        }
        $this->baseDirectory = $baseDirectory;
        
        FileHelper::createDirectory($this->baseDirectory);
        if (!is_dir($this->baseDirectory)) {
            throw new ApplicationException("Cannot create directory '{$this->baseDirectory}'");
        }
    }
    
    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }
    
    public function get(string $filename): ?Image
    {
        $sourceFilename = $this->composeSourceFilename($filename);
        
        if (!file_exists($sourceFilename)) {
            return null;
        }

        /**
         * @todo check for cached file
         */

        // Copying file by contents for testing purposes.
        // Actually, FileSource needed only for local testing.
        $data = file_get_contents($sourceFilename);
        $tempFilename = $this->temporaryFilesManager->putFile($filename, $data);
        
        return new Image($tempFilename);
    }

    private function composeSourceFilename(string $filename): string
    {
        $sourceFilename = $this->baseDirectory;

        if (substr($filename, 0, 1) === '/') {
            $sourceFilename .= $filename;
        } else {
            $sourceFilename .= '/' . $filename;
        }

        return $sourceFilename;
    }

}
