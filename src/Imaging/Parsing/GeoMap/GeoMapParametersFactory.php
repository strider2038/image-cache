<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\GeoMap;

use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GeoMapParametersFactory implements GeoMapParametersFactoryInterface
{
    /** @var string */
    private $defaultType = 'roadmap';
    /** @var float */
    private $defaultLatitude;
    /** @var float */
    private $defaultLongitude;
    /** @var int */
    private $defaultZoom = 14;
    /** @var int */
    private $defaultWidth = 600;
    /** @var int */
    private $defaultHeight = 450;
    /** @var float */
    private $defaultScale = 1.0;

    public function setDefaultType(string $defaultType): void
    {
        $this->defaultType = $defaultType;
    }

    public function setDefaultLatitude(float $defaultLatitude): void
    {
        $this->defaultLatitude = $defaultLatitude;
    }

    public function setDefaultLongitude(float $defaultLongitude): void
    {
        $this->defaultLongitude = $defaultLongitude;
    }

    public function setDefaultZoom(int $defaultZoom): void
    {
        $this->defaultZoom = $defaultZoom;
    }

    public function setDefaultWidth(int $defaultWidth): void
    {
        $this->defaultWidth = $defaultWidth;
    }

    public function setDefaultHeight(int $defaultHeight): void
    {
        $this->defaultHeight = $defaultHeight;
    }

    public function setDefaultScale(float $defaultScale): void
    {
        $this->defaultScale = $defaultScale;
    }

    public function createGeoMapParameters(): GeoMapParameters
    {
        $parameters = new GeoMapParameters();
        $parameters->type = $this->defaultType;
        $parameters->latitude = $this->defaultLatitude;
        $parameters->longitude = $this->defaultLongitude;
        $parameters->zoom = $this->defaultZoom;
        $parameters->width = $this->defaultWidth;
        $parameters->height = $this->defaultHeight;
        $parameters->scale = $this->defaultScale;

        return $parameters;
    }
}
