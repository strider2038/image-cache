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
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParameters;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapParametersFactory implements YandexMapParametersFactoryInterface
{
    /** @var EntityValidatorInterface */
    private $validator;

    /** @var array */
    private const MAP_TYPE_AND_LAYERS_MAP = [
        'roadmap' => ['map'],
        'satellite' => ['sat'],
        'hybrid' => ['map', 'sat'],
    ];

    public function __construct(EntityValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function createYandexMapParametersFromGeoMapParameters(
        GeoMapParameters $geoMapParameters
    ): YandexMapParameters {
        $yandexMapParameters = new YandexMapParameters();

        $yandexMapParameters->layers = $this->getLayersForMapType($geoMapParameters->type);
        $yandexMapParameters->latitude = $geoMapParameters->latitude;
        $yandexMapParameters->longitude = $geoMapParameters->longitude;
        $yandexMapParameters->zoom = $geoMapParameters->zoom;
        $yandexMapParameters->width = $geoMapParameters->width;
        $yandexMapParameters->height = $geoMapParameters->height;
        $yandexMapParameters->scale = $geoMapParameters->scale;

        $this->validator->validateWithException($yandexMapParameters, InvalidRequestValueException::class);

        return $yandexMapParameters;
    }

    private function getLayersForMapType(string $mapType): StringList
    {
        return new StringList(self::MAP_TYPE_AND_LAYERS_MAP[$mapType] ?? []);
    }
}
