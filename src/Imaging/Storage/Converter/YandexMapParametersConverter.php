<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Converter;

use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParametersFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapParametersConverter implements GeoMapParametersConverterInterface
{
    /** @var YandexMapParametersFactoryInterface */
    private $yandexMapParametersFactory;

    public function __construct(YandexMapParametersFactoryInterface $yandexMapParametersFactory)
    {
        $this->yandexMapParametersFactory = $yandexMapParametersFactory;
    }

    public function convertGeoMapParametersToQuery(GeoMapParameters $parameters): QueryParameterCollection
    {
        $yandexMapParameters = $this->yandexMapParametersFactory->createYandexMapParametersFromGeoMapParameters($parameters);

        return $this->createQueryByParameters($yandexMapParameters);
    }

    private function createQueryByParameters(YandexMapParameters $yandexMapParameters): QueryParameterCollection
    {
        return new QueryParameterCollection([
            new QueryParameter('l', $yandexMapParameters->layers->implode()),
            new QueryParameter('ll', sprintf(
                '%s,%s',
                $yandexMapParameters->longitude,
                $yandexMapParameters->latitude
            )),
            new QueryParameter('z', $yandexMapParameters->zoom),
            new QueryParameter('size', sprintf(
                '%d,%d',
                $yandexMapParameters->width,
                $yandexMapParameters->height
            )),
            new QueryParameter('scale', $yandexMapParameters->scale)
        ]);
    }
}
