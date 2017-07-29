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

use Strider2038\ImgCache\Exception\InvalidConfigException;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;

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
            throw new InvalidConfigException("Directory '{$this->baseDirectory}' does not exist");
        }

        $this->imageFactory = $imageFactory;
    }
    
    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }
    
    public function get(FilenameKeyInterface $key): ?ImageInterface
    {
        $sourceFilename = $this->composeSourceFilename($key);
        
        if (!file_exists($sourceFilename)) {
            return null;
        }

        return $this->imageFactory->createImageFile($sourceFilename);
    }

    public function exists(FilenameKeyInterface $key): bool
    {
        $sourceFilename = $this->composeSourceFilename($key);

        return file_exists($sourceFilename);
    }

    private function composeSourceFilename(FilenameKeyInterface $key): string
    {
        $sourceFilename = $this->baseDirectory . $key->getValue();

        return $sourceFilename;
    }

}