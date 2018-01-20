<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class AbstractImageSource
{
    /** @var string */
    private $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }
}
