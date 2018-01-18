<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional;

use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Imaging\Parsing\ImageParametersModifier;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageParametersModifierTest extends FunctionalTestCase
{
    /** @var ImageParametersModifier */
    private $imageParametersModifier;

    protected function setUp(): void
    {
        $container = $this->loadContainer('image-parameters-modifier.yml');
        $this->imageParametersModifier = $container->get('image_parameters_modifier');
    }

    /**
     * @test
     * @param string $stringParameters
     * @param int $qualityValue
     * @dataProvider imageParametersProvider
     */
    public function updateParametersByConfiguration_givenImageParametersAndStringParameters_stringParametersParsedAndImageParametersUpdated(
        string $stringParameters,
        int $qualityValue
    ): void {
        $imageParameters = new ImageParameters();

        $this->imageParametersModifier->updateParametersByConfiguration($imageParameters, $stringParameters);

        $this->assertEquals($qualityValue, $imageParameters->getQuality());
    }

    public function imageParametersProvider(): array
    {
        return [
            ['', ImageParameters::QUALITY_VALUE_DEFAULT],
            ['quality', ImageParameters::QUALITY_VALUE_DEFAULT],
            ['quality75', 75],
            ['q65', 65],
        ];
    }
}
