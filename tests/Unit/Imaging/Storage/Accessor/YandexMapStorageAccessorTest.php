<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Accessor;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\YandexMapStorageAccessor;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Driver\YandexMapStorageDriverInterface;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class YandexMapStorageAccessorTest extends TestCase
{
    use LoggerTrait;

    private const LAYER = 'layer';
    private const LONGITUDE = 1.0;
    private const LATITUDE = -1.0;
    private const ZOOM = 4;
    private const WIDTH = 150;
    private const HEIGHT = 100;
    private const SCALE = 2.5;
    private const EXPECTED_QUERY_PARAMETERS = [
        'l' => self::LAYER,
        'll' => self::LONGITUDE . ',' . self::LATITUDE,
        'z' => self::ZOOM,
        'size' => self::WIDTH . ',' . self::HEIGHT,
        'scale' => self::SCALE,
    ];

    /** @var EntityValidatorInterface */
    private $validator;

    /** @var YandexMapStorageDriverInterface */
    private $storageDriver;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
        $this->storageDriver = \Phake::mock(YandexMapStorageDriverInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function getImage_givenValidParameters_sourceGetIsCalledWithQueryParameters(): void
    {
        $accessor = $this->createYandexMapStorageAccessor();
        $parameters = $this->givenParameters();
        $stream = $this->givenStorageDriver_getMapContents_returnsStream();
        $expectedImage = $this->givenImageFactory_createImageFromStream_returnsImage();

        $image = $accessor->getImage($parameters);

        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            YandexMapParameters::class,
            InvalidRequestValueException::class
        );
        $this->assertStorageDriver_getMapContents_isCalledOnceWithQueryParameters(self::EXPECTED_QUERY_PARAMETERS);
        $this->assertImageFactory_createImageFromStream_isCalledOnceWithStream($stream);
        $this->assertLogger_info_isCalledOnce($this->logger);
        $this->assertSame($expectedImage, $image);
    }

    private function createYandexMapStorageAccessor(): YandexMapStorageAccessor
    {
        $accessor = new YandexMapStorageAccessor(
            $this->validator,
            $this->storageDriver,
            $this->imageFactory
        );

        $accessor->setLogger($this->logger);

        return $accessor;
    }

    private function givenParameters(): YandexMapParameters
    {
        $parameters = new YandexMapParameters();
        $parameters->setLayers(new StringList([self::LAYER]));
        $parameters->setLongitude(self::LONGITUDE);
        $parameters->setLatitude(self::LATITUDE);
        $parameters->setZoom(self::ZOOM);
        $parameters->setWidth(self::WIDTH);
        $parameters->setHeight(self::HEIGHT);
        $parameters->setScale(self::SCALE);

        return $parameters;
    }

    private function assertStorageDriver_getMapContents_isCalledOnceWithQueryParameters(
        array $expectedQueryParameters
    ): void {
        /** @var QueryParameterCollection $queryParameters */
        \Phake::verify($this->storageDriver, \Phake::times(1))
            ->getMapContents(\Phake::capture($queryParameters));

        $this->assertEquals($expectedQueryParameters, $queryParameters->toArray());
    }

    private function givenStorageDriver_getMapContents_returnsStream(): StreamInterface
    {
        $image = \Phake::mock(StreamInterface::class);
        \Phake::when($this->storageDriver)->getMapContents(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }

    private function assertImageFactory_createImageFromStream_isCalledOnceWithStream(StreamInterface $stream): void
    {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createImageFromStream($stream);
    }

    private function givenImageFactory_createImageFromStream_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createImageFromStream(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }

    private function assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
        string $entityClass,
        string $exceptionClass
    ): void {
        \Phake::verify($this->validator, \Phake::times(1))
            ->validateWithException(\Phake::capture($entity), \Phake::capture($exception));
        $this->assertInstanceOf($entityClass, $entity);
        $this->assertEquals($exceptionClass, $exception);
    }
}
