<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Yandex;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\ValueConfiguratorFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\Map\ValueConfiguratorInterface;
use Strider2038\ImgCache\Imaging\Parsing\Yandex\YandexMapParametersParser;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParameters;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapParametersFactoryInterface;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;

class YandexMapParametersParserTest extends TestCase
{
    private const INVALID_KEY = 'key';

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var ValueConfiguratorFactoryInterface */
    private $valueConfiguratorFactory;

    /** @var YandexMapParametersFactoryInterface */
    private $parametersFactory;

    protected function setUp(): void
    {
        $this->imageValidator = \Phake::mock(ImageValidatorInterface::class);
        $this->valueConfiguratorFactory = \Phake::mock(ValueConfiguratorFactoryInterface::class);
        $this->parametersFactory = \Phake::mock(YandexMapParametersFactoryInterface::class);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Unsupported image extension
     */
    public function parse_givenKeyWithInvalidExtension_exceptionThrown(): void
    {
        $parser = $this->createParser();
        $this->givenImageValidator_hasValidImageExtension_returns(self::INVALID_KEY, false);

        $parser->parse(self::INVALID_KEY);
    }

    /**
     * @test
     * @dataProvider keyAndCallParametersProvider
     * @param string $key
     * @param string $parameterName
     * @param string $parameterValue
     */
    public function parse_givenKey_keysAndValuesAreParsedAndParametersAreReturned(
        string $key,
        string $parameterName,
        string $parameterValue
    ): void {
        $parser = $this->createParser();
        $expectedParameters = $this->givenParametersFactory_create_returnsParameters();
        $configurator = $this->givenValueConfiguratorFactory_create_returnsValueConfigurator($parameterName);
        $this->givenImageValidator_hasValidImageExtension_returns($key, true);

        $parameters = $parser->parse($key);

        $this->assertSame($expectedParameters, $parameters);
        $this->assertValueConfigurator_configure_isCalledOnceWith($parameterValue, $configurator, $expectedParameters);
    }

    public function keyAndCallParametersProvider(): array
    {
        return [
            ['key=1.jpg', 'key', '1'],
            ['/k=v.jpg', 'k', 'v'],
            ['/k=.jpg', 'k', ''],
            ['/ll=37.620070,55.753630.jpg', 'll', '37.620070,55.753630'],
            ['/directory/subdirectory/key=value.jpg', 'key', 'value'],
        ];
    }

    /**
     * @test
     * @dataProvider keyAndTimesProvider
     * @param string $key
     * @param int $expectedTimes
     */
    public function parse_givenKeyWithManyParameters_keysAndValuesAreParsedExpectedTimes(
        string $key,
        int $expectedTimes
    ): void {
        $parser = $this->createParser();
        $expectedParameters = $this->givenParametersFactory_create_returnsParameters();
        $this->givenValueConfiguratorFactory_create_returnsValueConfigurator(\Phake::anyParameters());
        $this->givenImageValidator_hasValidImageExtension_returns($key, true);

        $parameters = $parser->parse($key);

        $this->assertSame($expectedParameters, $parameters);
        $this->assertValueConfiguratorFactory_create_isCalledTimes($expectedTimes);
    }

    public function keyAndTimesProvider(): array
    {
        return [
            ['key=1.jpg', 1],
            ['a=1_b=2.jpg', 2],
            ['a=1_b=2_c=3.jpg', 3],
        ];
    }

    private function createParser(): YandexMapParametersParser
    {
        return new YandexMapParametersParser(
            $this->imageValidator,
            $this->valueConfiguratorFactory,
            $this->parametersFactory
        );
    }

    private function givenParametersFactory_create_returnsParameters(): YandexMapParameters
    {
        $parameters = \Phake::mock(YandexMapParameters::class);
        \Phake::when($this->parametersFactory)->create()->thenReturn($parameters);

        return $parameters;
    }

    private function givenValueConfiguratorFactory_create_returnsValueConfigurator(
        $parameterName
    ): ValueConfiguratorInterface {
        $configurator = \Phake::mock(ValueConfiguratorInterface::class);
        \Phake::when($this->valueConfiguratorFactory)->create($parameterName)->thenReturn($configurator);

        return $configurator;
    }

    private function assertValueConfigurator_configure_isCalledOnceWith(
        string $parameterValue,
        ValueConfiguratorInterface $configurator,
        YandexMapParameters $expectedParameters
    ): void {
        \Phake::verify($configurator, \Phake::times(1))->configure($parameterValue, $expectedParameters);
    }

    private function assertValueConfiguratorFactory_create_isCalledTimes(int $expectedTimes): void
    {
        \Phake::verify($this->valueConfiguratorFactory, \Phake::times($expectedTimes))->create(\Phake::anyParameters());
    }

    /**
     * @param $filename
     * @param $value
     */
    private function givenImageValidator_hasValidImageExtension_returns($filename, $value): void
    {
        \Phake::when($this->imageValidator)->hasValidImageExtension($filename)->thenReturn($value);
    }
}
