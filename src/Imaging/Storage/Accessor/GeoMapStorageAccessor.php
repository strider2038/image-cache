<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Accessor;

use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Converter\GeoMapParametersConverterInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\ApiStorageDriverInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GeoMapStorageAccessor implements GeoMapStorageAccessorInterface
{
    /** @var GeoMapParametersConverterInterface */
    private $parametersConverter;

    /** @var ApiStorageDriverInterface */
    private $storageDriver;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    public function __construct(
        GeoMapParametersConverterInterface $parametersConverter,
        ApiStorageDriverInterface $storageDriver,
        ImageFactoryInterface $imageFactory
    ) {
        $this->parametersConverter = $parametersConverter;
        $this->storageDriver = $storageDriver;
        $this->imageFactory = $imageFactory;
    }

    public function getImage(GeoMapParameters $parameters): Image
    {
        $query = $this->parametersConverter->convertGeoMapParametersToQuery($parameters);
        $imageContents = $this->storageDriver->getImageContents($query);

        return $this->imageFactory->createImageFromStream($imageContents);
    }
}
