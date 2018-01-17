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

use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParameters;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface GeoMapParametersConverterInterface
{
    public function convertGeoMapParametersToQuery(GeoMapParameters $parameters): QueryParameterCollection;
}
