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
class Configuration
{
    /** @var string */
    private $accessControlToken;

    /** @var int */
    private $cachedImageQuality;

    /** @var ImageSourceCollection */
    private $sourceCollection;

    public function __construct(
        string $accessControlToken,
        int $cachedImageQuality,
        ImageSourceCollection $sourceCollection
    ) {
        $this->accessControlToken = $accessControlToken;
        $this->cachedImageQuality = $cachedImageQuality;
        $this->sourceCollection = $sourceCollection;
    }

    public function getAccessControlToken(): string
    {
        return $this->accessControlToken;
    }

    public function getCachedImageQuality(): int
    {
        return $this->cachedImageQuality;
    }

    public function getSourceCollection(): ImageSourceCollection
    {
        return $this->sourceCollection;
    }
}
