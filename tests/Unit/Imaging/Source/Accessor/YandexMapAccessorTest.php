<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Source\Accessor;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\QueryParametersCollection;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\YandexMapAccessor;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapSourceInterface;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationsFormatterInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class YandexMapAccessorTest extends TestCase
{
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

    /** @var YandexMapSourceInterface */
    private $source;

    protected function setUp()
    {
        $this->validator = \Phake::mock(ModelValidatorInterface::class);
        $this->formatter = \Phake::mock(ViolationsFormatterInterface::class);
        $this->source = \Phake::mock(YandexMapSourceInterface::class);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid map parameters: formatted violations
     */
    public function get_givenInvalidParameters_exceptionThrown(): void
    {
        $source = $this->createAccessor();
        $parameters = $this->givenParameters();
        $violations = $this->givenValidator_validate_returnViolations($parameters);
        $this->givenViolations_count_returns($violations, 1);
        \Phake::when($this->formatter)->format($violations)->thenReturn('formatted violations');

        $source->get($parameters);
    }

    /** @test */
    public function get_givenValidParameters_sourceGetIsCalledWithQueryParameters(): void
    {
        $source = $this->createAccessor();
        $parameters = $this->givenParameters();
        $this->givenValidator_validate_returnViolations($parameters);
        $expectedImage = $this->givenSource_get_returnsImage();

        $image = $source->get($parameters);

        $this->assertInstanceOf(ImageInterface::class, $image);
        $this->assertSame($expectedImage, $image);
        $this->assertSource_get_isCalledOnceWithQueryParameters();
    }

    private function createAccessor(): YandexMapAccessor
    {
        return new YandexMapAccessor($this->validator, $this->formatter, $this->source);
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

    private function assertSource_get_isCalledOnceWithQueryParameters(): void
    {
        /** @var QueryParametersCollection $queryParameters */
        \Phake::verify($this->source, \Phake::times(1))
            ->get(\Phake::capture($queryParameters));

        $this->assertEquals(self::EXPECTED_QUERY_PARAMETERS, $queryParameters->toArray());
    }

    private function givenSource_get_returnsImage(): ImageInterface
    {
        $image = \Phake::mock(ImageInterface::class);
        \Phake::when($this->source)->get(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }
}
