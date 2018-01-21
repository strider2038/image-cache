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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class AbstractImageSource implements EntityInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^\/.*$/i",
     *     message="Cache directory name must start with slash"
     * )
     * @var string
     */
    private $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    abstract public function getImageStorageServiceId(): string;

    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }
}
