<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source\Yandex;

use Strider2038\ImgCache\Collection\StringList;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapParameters implements YandexMapParametersInterface
{
    /** @var StringList */
    private $layers;

    /** @var float */
    private $longitude = 0;

    /** @var float */
    private $latitude = 0;

    /** @var int */
    private $zoom = 0;

    /** @var int */
    private $width = 0;

    /** @var int */
    private $height = 0;

    /** @var float */
    private $scale = 0;

    public function __construct()
    {
        $this->layers = new StringList();
    }

    public function getLayers(): StringList
    {
        return $this->layers;
    }

    public function setLayers(StringList $layers): void
    {
        $this->layers = $layers;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }

    public function setZoom(int $zoom): void
    {
        $this->zoom = $zoom;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getScale(): float
    {
        return $this->scale;
    }

    public function setScale(float $scale): void
    {
        $this->scale = $scale;
    }
}
