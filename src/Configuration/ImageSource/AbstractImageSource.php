<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration\ImageSource;

use Strider2038\ImgCache\Core\EntityInterface;
use Strider2038\ImgCache\Imaging\Naming\DirectoryName;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class AbstractImageSource implements EntityInterface
{
    /**
     * @Assert\Valid()
     * @var DirectoryNameInterface
     */
    private $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $cacheDirectory = $cacheDirectory === '' ? '' : rtrim($cacheDirectory, '/') . '/';
        $this->cacheDirectory = new DirectoryName($cacheDirectory);
    }

    abstract public function getImageStorageServiceId(): string;

    public function getCacheDirectory(): DirectoryNameInterface
    {
        return $this->cacheDirectory;
    }
}
