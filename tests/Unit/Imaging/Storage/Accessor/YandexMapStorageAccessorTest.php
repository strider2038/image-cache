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
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Storage\Accessor\YandexMapStorageAccessor;
use Strider2038\ImgCache\Imaging\Storage\Data\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Driver\YandexMapStorageDriverInterface;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationsFormatterInterface;
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

    /** @var ViolationsFormatterInterface */
    private $formatter;

    /** @var YandexMapStorageDriverInterface */
    private $storageDriver;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(ModelValidatorInterface::class);
        $this->formatter = \Phake::mock(ViolationsFormatterInterface::class);
        $this->storageDriver = \Phake::mock(YandexMapStorageDriverInterface::class);
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
        $violations = $this->givenValidator_validate_returnViolations($parameters);
        $this->givenViolations_count_returns($violations, 1);
        \Phake::when($this->formatter)->format($violations)->thenReturn('formatted violations');

        $accessor->getImage($parameters);
    }

    /** @test */
    public function getImage_givenValidParameters_sourceGetIsCalledWithQueryParameters(): void
    {
        $accessor = $this->createYandexMapStorageAccessor();
        $parameters = $this->givenParameters();
        $this->givenValidator_validate_returnViolations($parameters);
        $expectedImage = $this->givenStorageDriver_get_returnsImage();

        $image = $accessor->getImage($parameters);

        $this->assertSame($expectedImage, $image);
        $this->assertStorageDriver_get_isCalledOnceWithQueryParameters();
        $this->assertLogger_info_isCalledOnce($this->logger);
    }

    private function createYandexMapStorageAccessor(): YandexMapStorageAccessor
    {
        $accessor = new YandexMapStorageAccessor($this->validator, $this->formatter, $this->storageDriver);
        $accessor->setLogger($this->logger);

        return $accessor;
    }

    private function givenValidator_validate_returnViolations(
        YandexMapParameters $parameters
    ): ConstraintViolationListInterface {
        $violations = \Phake::mock(ConstraintViolationListInterface::class);
        \Phake::when($this->validator)->validate($parameters)->thenReturn($violations);

        return $violations;
    }

    private function givenViolations_count_returns(ConstraintViolationListInterface $violations, int $count): void
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

    private function assertStorageDriver_get_isCalledOnceWithQueryParameters(): void
    {
        /** @var QueryParametersCollection $queryParameters */
        \Phake::verify($this->storageDriver, \Phake::times(1))
            ->get(\Phake::capture($queryParameters));

        $this->assertEquals(self::EXPECTED_QUERY_PARAMETERS, $queryParameters->toArray());
    }

    private function givenStorageDriver_get_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->storageDriver)->get(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }
}
