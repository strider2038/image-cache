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
use Strider2038\ImgCache\Core\QueryParametersCollection;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\YandexMapStorageAccessor;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Driver\YandexMapStorageDriverInterface;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationFormatterInterface;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Symfony\Component\Validator\ConstraintViolationListInterface;

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

    /** @var ModelValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationsFormatter;

    /** @var YandexMapStorageDriverInterface */
    private $storageDriver;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(ModelValidatorInterface::class);
        $this->violationsFormatter = \Phake::mock(ViolationFormatterInterface::class);
        $this->storageDriver = \Phake::mock(YandexMapStorageDriverInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->logger = $this->givenLogger();
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid map parameters: formatted violations
     */
    public function getImage_givenInvalidParameters_exceptionThrown(): void
    {
        $accessor = $this->createYandexMapStorageAccessor();
        $parameters = $this->givenParameters();
        $violations = $this->givenValidator_validateModel_returnViolations($parameters);
        $this->givenViolations_count_returnsCount($violations, 1);
        $this->givenViolationFormatter_formatViolations_returnsString($violations, 'formatted violations');

        $accessor->getImage($parameters);
    }

    /** @test */
    public function getImage_givenValidParameters_sourceGetIsCalledWithQueryParameters(): void
    {
        $accessor = $this->createYandexMapStorageAccessor();
        $parameters = $this->givenParameters();
        $this->givenValidator_validateModel_returnViolations($parameters);
        $stream = $this->givenStorageDriver_getMapContents_returnsStream();
        $expectedImage = $this->givenImageFactory_createFromStream_returnsImage();

        $image = $accessor->getImage($parameters);

        $this->assertStorageDriver_getMapContents_isCalledOnceWithQueryParameters(self::EXPECTED_QUERY_PARAMETERS);
        $this->assertImageFactory_createFromStream_isCalledOnceWithStream($stream);
        $this->assertLogger_info_isCalledOnce($this->logger);
        $this->assertSame($expectedImage, $image);
    }

    private function createYandexMapStorageAccessor(): YandexMapStorageAccessor
    {
        $accessor = new YandexMapStorageAccessor(
            $this->validator,
            $this->violationsFormatter,
            $this->storageDriver,
            $this->imageFactory
        );

        $accessor->setLogger($this->logger);

        return $accessor;
    }

    private function givenValidator_validateModel_returnViolations(
        YandexMapParameters $parameters
    ): ConstraintViolationListInterface {
        $violations = \Phake::mock(ConstraintViolationListInterface::class);
        \Phake::when($this->validator)->validateModel($parameters)->thenReturn($violations);

        return $violations;
    }

    private function givenViolations_count_returnsCount(ConstraintViolationListInterface $violations, int $count): void
    {
        \Phake::when($violations)->count()->thenReturn($count);
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
        /** @var QueryParametersCollection $queryParameters */
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

    private function givenViolationFormatter_formatViolations_returnsString(
        ConstraintViolationListInterface $violationList,
        string $violations
    ): void {
        \Phake::when($this->violationsFormatter)->formatViolations($violationList)->thenReturn($violations);
    }

    private function assertImageFactory_createFromStream_isCalledOnceWithStream(StreamInterface $stream): void
    {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createFromStream($stream);
    }

    private function givenImageFactory_createFromStream_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createFromStream(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }
}
