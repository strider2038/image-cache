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
class YandexMapParametersFactory implements YandexMapParametersFactoryInterface
{
    private const DEFAULT_LAYERS = ['map'];
    private const DEFAULT_LONGITUDE = 0;
    private const DEFAULT_LATITUDE = 0;
    private const DEFAULT_ZOOM = 0;
    private const DEFAULT_WIDTH = 450;
    private const DEFAULT_HEIGHT = 300;
    private const DEFAULT_SCALE = 1.0;

    /** @var StringList */
    private $layers;

    /** @var float */
    private $longitude = self::DEFAULT_LONGITUDE;

    /** @var float */
    private $latitude = self::DEFAULT_LATITUDE;

    /** @var int */
    private $zoom = self::DEFAULT_ZOOM;

    /** @var int */
    private $width = self::DEFAULT_WIDTH;

    /** @var int */
    private $height = self::DEFAULT_HEIGHT;

    /** @var float */
    private $scale = self::DEFAULT_SCALE;

    public function __construct()
    {
        $this->layers = new StringList(self::DEFAULT_LAYERS);
    }

    public function setLayers(StringList $layers): void
    {
        $this->layers = $layers;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function setZoom(int $zoom): void
    {
        $this->zoom = $zoom;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function setScale(float $scale): void
    {
        $this->scale = $scale;
    }

    public function create(): YandexMapParameters
    {
        $parameters = new YandexMapParameters();
        $parameters->setLayers($this->layers);
        $parameters->setLongitude($this->longitude);
        $parameters->setLatitude($this->latitude);
        $parameters->setZoom($this->zoom);
        $parameters->setWidth($this->width);
        $parameters->setHeight($this->height);
        $parameters->setScale($this->scale);

        return $parameters;
    }
}
