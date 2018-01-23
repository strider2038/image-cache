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

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GeoMapImageSource extends AbstractImageSource
{
    /**
     * @Assert\Choice(
     *     choices={"yandex"},
     *     strict=true
     * )
     * @var string
     */
    private $driver;

    /** @var string */
    private $apiKey;

    public function __construct(
        string $cacheDirectory,
        string $driver,
        string $apiKey
    ) {
        parent::__construct($cacheDirectory);
        $this->driver = $driver;
        $this->apiKey = $apiKey;
    }

    public function getId(): string
    {
        return 'geographical map image source';
    }

    public function getImageStorageServiceId(): string
    {
        return 'geo_map_storage';
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}