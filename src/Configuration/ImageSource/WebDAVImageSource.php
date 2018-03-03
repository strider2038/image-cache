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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class WebDAVImageSource extends FilesystemImageSource
{
    /** @var string */
    private $driverUri;

    /** @var string */
    private $oauthToken;

    public function __construct(
        string $cacheDirectory,
        string $storageDirectory,
        string $processorType,
        string $driverUri,
        string $oauthToken
    ) {
        parent::__construct($cacheDirectory, $storageDirectory, $processorType);
        $this->driverUri = $driverUri;
        $this->oauthToken = $oauthToken;
    }

    public function getDriverUri(): string
    {
        return $this->driverUri;
    }

    public function getOauthToken(): string
    {
        return $this->oauthToken;
    }
}
