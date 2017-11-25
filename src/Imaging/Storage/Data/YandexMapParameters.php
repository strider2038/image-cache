<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Data;

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\ModelInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapParameters implements ModelInterface, \JsonSerializable
{
    /**
     * @Assert\Count(min = 1)
     * @Assert\All({
     *     @Assert\Choice(
     *      choices={"map", "sat", "skl", "trf"},
     *      strict=true
     *     )
     * })
     * @return StringList
     */
    private $layers;

    /**
     * @Assert\Range(min = -180, max = 180)
     * @return float
     */
    private $longitude = 0;

    /**
     * @Assert\Range(min = -180, max = 180)
     * @return float
     */
    private $latitude = 0;

    /**
     * @Assert\Range(min = 0, max = 17)
     * @return int
     */
    private $zoom = 0;

    /**
     * @Assert\Range(min = 50, max = 650)
     * @return int
     */
    private $width = 0;

    /**
     * @Assert\Range(min = 50, max = 450)
     * @return int
     */
    private $height = 0;

    /**
     * @Assert\Range(min = 1.0, max = 4.0)
     * @return float
     */
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

    public function jsonSerialize(): array
    {
        return [
            'layers' => $this->layers->toArray(),
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'zoom' => $this->zoom,
            'width' => $this->width,
            'height' => $this->height,
            'scale' => $this->scale,
        ];
    }
}
