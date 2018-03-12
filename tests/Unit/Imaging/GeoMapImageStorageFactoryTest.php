<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Imaging\GeoMapImageStorageFactory;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageStorage;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParametersParserInterface;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\HttpClientFactoryInterface;
use Strider2038\ImgCache\Utility\HttpClientInterface;

class GeoMapImageStorageFactoryTest extends TestCase
{
    use LoggerTrait;

    private const CACHE_DIRECTORY = 'cache_directory';
    private const DRIVER = 'yandex';
    private const API_KEY = 'api_key';
    private const YANDEX_MAP_BASE_URI = 'https://static-maps.yandex.ru/1.x/';

    /** @var GeoMapParametersParserInterface */
    private $parametersParser;
    /** @var EntityValidatorInterface */
    private $validator;
    /** @var ImageFactoryInterface */
    private $imageFactory;
    /** @var HttpClientFactoryInterface */
    private $httpClientFactory;

    protected function setUp(): void
    {
        $this->parametersParser = \Phake::mock(GeoMapParametersParserInterface::class);
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->httpClientFactory = \Phake::mock(HttpClientFactoryInterface::class);
    }

    /** @test */
    public function createImageStorageForImageSource_givenGeoMapImageSource_imageStorageCreatedAndReturned(): void
    {
        $imageStorageFactory = $this->createGeoMapImageStorageFactory();
        $imageSource = $this->givenGeoMapImageSource();
        $this->givenHttpClientFactory_createClient_returnsHttpClient();

        $imageStorage = $imageStorageFactory->createImageStorageForImageSource($imageSource);

        $this->assertInstanceOf(ImageStorage::class, $imageStorage);
        $this->assertHttpClientFactory_createClient_isCalledOnceWithExpectedParameters();
    }

    private function createGeoMapImageStorageFactory(): GeoMapImageStorageFactory
    {
        $factory = new GeoMapImageStorageFactory(
            $this->parametersParser,
            $this->validator,
            $this->imageFactory,
            $this->httpClientFactory
        );
        $factory->setLogger($this->givenLogger());

        return $factory;
    }

    private function givenGeoMapImageSource(): GeoMapImageSource
    {
        return new GeoMapImageSource(
            self::CACHE_DIRECTORY,
            self::DRIVER,
            self::API_KEY
        );
    }

    private function givenHttpClientFactory_createClient_returnsHttpClient(): void
    {
        $client = \Phake::mock(HttpClientInterface::class);
        \Phake::when($this->httpClientFactory)
            ->createClient(\Phake::anyParameters())
            ->thenReturn($client);
    }

    private function assertHttpClientFactory_createClient_isCalledOnceWithExpectedParameters(): void
    {
        $parameters = [
            'base_uri' => self::YANDEX_MAP_BASE_URI,
        ];

        \Phake::verify($this->httpClientFactory, \Phake::times(1))
            ->createClient($parameters);
    }
}
