<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging;

use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Imaging\Extraction\GeoMapExtractor;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParametersParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\GeoMapStorageAccessor;
use Strider2038\ImgCache\Imaging\Storage\Converter\YandexMapParametersConverter;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParametersFactory;
use Strider2038\ImgCache\Imaging\Storage\Driver\ApiStorageDriver;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\HttpClientFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GeoMapImageStorageFactory
{
    private const YANDEX_MAP_BASE_URI = 'https://static-maps.yandex.ru/1.x/';

    /** @var GeoMapParametersParserInterface */
    private $parametersParser;
    /** @var EntityValidatorInterface */
    private $validator;
    /** @var ImageFactoryInterface */
    private $imageFactory;
    /** @var HttpClientFactoryInterface */
    private $httpClientFactory;

    public function __construct(
        GeoMapParametersParserInterface $parametersParser,
        EntityValidatorInterface $validator,
        ImageFactoryInterface $imageFactory,
        HttpClientFactoryInterface $httpClientFactory
    ) {
        $this->parametersParser = $parametersParser;
        $this->validator = $validator;
        $this->imageFactory = $imageFactory;
        $this->httpClientFactory = $httpClientFactory;
    }

    public function createImageStorageForImageSource(GeoMapImageSource $imageSource): ImageStorageInterface
    {
        $parametersConverter = new YandexMapParametersConverter(
            new YandexMapParametersFactory(
                $this->validator
            )
        );

        $httpClient = $this->httpClientFactory->createClient([
            'base_uri' => self::YANDEX_MAP_BASE_URI
        ]);

        $storageDriver = new ApiStorageDriver(
            $httpClient,
            new QueryParameterCollection([
                new QueryParameter('key', $imageSource->getApiKey())
            ])
        );

        return new ImageStorage(
            new GeoMapExtractor(
                $this->parametersParser,
                new GeoMapStorageAccessor(
                    $parametersConverter,
                    $storageDriver,
                    $this->imageFactory
                )
            )
        );
    }
}
