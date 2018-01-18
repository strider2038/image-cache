<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Imaging\Parsing\ImageParametersModifier;
use Strider2038\ImgCache\Imaging\Parsing\StringParametersParserInterface;

class ImageParametersModifierTest extends TestCase
{
    private const STRING_PARAMETERS = 'string_parameters';
    private const PARSING_PATTERN = '/^(q|quality)(?P<quality>[0-9]+)$/';

    /** @var StringParametersParserInterface */
    private $parametersParser;

    protected function setUp(): void
    {
        $this->parametersParser = \Phake::mock(StringParametersParserInterface::class);
    }

    /**
     * @test
     * @param string $rawQualityValue
     * @param int $parsedQualityValue
     * @dataProvider qualityValuesProvider
     */
    public function updateParametersByConfiguration_givenImageParametersAndStringParameters_stringParametersParsedAndImageParametersUpdated(
        string $rawQualityValue,
        int $parsedQualityValue
    ): void {
        $modifier = new ImageParametersModifier($this->parametersParser);
        $imageParameters = \Phake::mock(ImageParameters::class);
        $parsedParameters = new StringList([
            'quality' => $rawQualityValue
        ]);
        $this->givenParametersParser_parseParameters_returnsParsedParameters($parsedParameters);

        $modifier->updateParametersByConfiguration($imageParameters, self::STRING_PARAMETERS);

        $this->assertStringParametersParser_parseParameters_isCalledOnceWithPatternAndStringParameters(
            self::PARSING_PATTERN,
            self::STRING_PARAMETERS
        );
        $this->assertImageParameters_setQuality_isCalledOnceWithValue($imageParameters, $parsedQualityValue);
    }

    public function qualityValuesProvider(): array
    {
        return [
            ['80', 80],
            ['', 0],
        ];
    }

    /** @test */
    public function updateParametersByConfiguration_givenImageParametersAndEmptyStringParameters_stringParametersParsedAndImageParametersNotUpdated(): void
    {
        $modifier = new ImageParametersModifier($this->parametersParser);
        $imageParameters = \Phake::mock(ImageParameters::class);
        $parsedParameters = new StringList();
        $this->givenParametersParser_parseParameters_returnsParsedParameters($parsedParameters);

        $modifier->updateParametersByConfiguration($imageParameters, self::STRING_PARAMETERS);

        $this->assertStringParametersParser_parseParameters_isCalledOnceWithPatternAndStringParameters(
            self::PARSING_PATTERN,
            self::STRING_PARAMETERS
        );
        \Phake::verifyNoInteraction($imageParameters);
    }

    private function givenParametersParser_parseParameters_returnsParsedParameters(
        StringList $parsedParameters
    ): void {
        \Phake::when($this->parametersParser)
            ->parseParameters(\Phake::anyParameters())
            ->thenReturn($parsedParameters);
    }

    private function assertStringParametersParser_parseParameters_isCalledOnceWithPatternAndStringParameters($pattern, $stringParameters): void
    {
        \Phake::verify($this->parametersParser, \Phake::times(1))
            ->parseParameters($pattern, $stringParameters);
    }

    private function assertImageParameters_setQuality_isCalledOnceWithValue(
        ImageParameters$imageParameters,
        int $parsedQualityValue
    ): void {
        \Phake::verify($imageParameters)->setQuality($parsedQualityValue);
    }
}
