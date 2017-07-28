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

use Strider2038\ImgCache\Exception\ApplicationException;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemSource implements FilesystemSourceInterface
{
    /** @var string */
    private $baseDirectory;

    /** @var ImageFactoryInterface */
    private $imageFactory;
    
    public function __construct(string $baseDirectory, ImageFactoryInterface $imageFactory) {
        $this->baseDirectory = rtrim($baseDirectory, '/') . '/';

        if (!is_dir($this->baseDirectory)) {
            throw new ApplicationException("Cannot create directory '{$this->baseDirectory}'");
        }

        $this->imageFactory = $imageFactory;
    }
    
    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }
    
    public function get(string $filename): ?ImageInterface
    {
        $sourceFilename = $this->composeSourceFilename($filename);
        
        if (!file_exists($sourceFilename)) {
            return null;
        }

        return $this->imageFactory->createImageFile($sourceFilename);
    }

    public function exists(string $filename): bool
    {
        // TODO: Implement exists() method.
    }

    private function composeSourceFilename(string $filename): string
    {
        $sourceFilename = $this->baseDirectory . $filename;

        return $sourceFilename;
    }

}
