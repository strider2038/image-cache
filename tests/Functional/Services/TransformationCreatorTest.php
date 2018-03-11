<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional\Services;

use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Imaging\Processing\Transforming\FlipTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\FlopTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ResizingTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\RotatingTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ShiftingTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationCreatorInterface;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationCreatorTest extends FunctionalTestCase
{
    /** @var TransformationCreatorInterface */
    private $transformationCreator;

    protected function setUp(): void
    {
        $container = $this->loadContainer('services/transformation-creator.yml');
        $this->transformationCreator = $container->get('transformation_creator');
    }

    /**
     * @test
     * @param string $configuration
     * @param string $transformationClass
     * @dataProvider configurationAndTransformationClassProvider
     */
    public function findAndCreateTransformation_givenStringConfiguration_expectedTransformationCreatedAndReturned(
        string $configuration,
        string $transformationClass
    ): void {
        $transformation = $this->transformationCreator->findAndCreateTransformation($configuration);

        $this->assertInstanceOf($transformationClass, $transformation);
    }

    public function configurationAndTransformationClassProvider(): array
    {
        return [
            ['s200x100', ResizingTransformation::class],
            ['size200x100', ResizingTransformation::class],
            ['flip', FlipTransformation::class],
            ['flop', FlopTransformation::class],
            ['r90', RotatingTransformation::class],
            ['rotate90', RotatingTransformation::class],
            ['sx5y2', ShiftingTransformation::class],
            ['shiftx5y2', ShiftingTransformation::class],
        ];
    }

    /**
     * @test
     * @param string $configuration
     * @param int $width
     * @param int $height
     * @param string $mode
     * @dataProvider resizingConfigurationProvider
     */
    public function findAndCreateTransformation_givenResizeConfiguration_resizingTransformationWithValidParametersCreated(
        string $configuration,
        int $width,
        int $height,
        string $mode
    ): void {
        /** @var ResizingTransformation $transformation */
        $transformation = $this->transformationCreator->findAndCreateTransformation($configuration);

        $this->assertNotNull($transformation);
        $this->assertInstanceOf(ResizingTransformation::class, $transformation);
        $this->assertEquals($width, $transformation->getParameters()->getWidth());
        $this->assertEquals($height, $transformation->getParameters()->getHeight());
        $this->assertEquals($mode, $transformation->getParameters()->getMode());
    }

    public function resizingConfigurationProvider(): array
    {
        return [
            ['s100x100f', 100, 100, ResizeModeEnum::FIT_IN],
            ['s500x200s', 500, 200, ResizeModeEnum::STRETCH],
            ['s50x1000w', 50, 1000, ResizeModeEnum::PRESERVE_WIDTH],
            ['s300x200h', 300, 200, ResizeModeEnum::PRESERVE_HEIGHT],
            ['s400X250H', 400, 250, ResizeModeEnum::PRESERVE_HEIGHT],
            ['s200x300', 200, 300, ResizeModeEnum::STRETCH],
            ['s200f', 200, 200, ResizeModeEnum::FIT_IN],
            ['s150', 150, 150, ResizeModeEnum::STRETCH],
            ['size100x100f', 100, 100, ResizeModeEnum::FIT_IN],
            ['size500x200s', 500, 200, ResizeModeEnum::STRETCH],
            ['size50x1000w', 50, 1000, ResizeModeEnum::PRESERVE_WIDTH],
            ['size300x200h', 300, 200, ResizeModeEnum::PRESERVE_HEIGHT],
            ['size400X250H', 400, 250, ResizeModeEnum::PRESERVE_HEIGHT],
            ['size200x300', 200, 300, ResizeModeEnum::STRETCH],
            ['size200f', 200, 200, ResizeModeEnum::FIT_IN],
            ['size150', 150, 150, ResizeModeEnum::STRETCH],
        ];
    }

    /**
     * @test
     * @param string $configuration
     * @param float $rotationDegree
     * @dataProvider rotatingConfigurationProvider
     */
    public function findAndCreateTransformation_givenRotatingConfiguration_rotatingTransformationWithValidParametersCreated(
        string $configuration,
        float $rotationDegree
    ): void {
        /** @var RotatingTransformation $transformation */
        $transformation = $this->transformationCreator->findAndCreateTransformation($configuration);

        $this->assertNotNull($transformation);
        $this->assertInstanceOf(RotatingTransformation::class, $transformation);
        $this->assertEquals($rotationDegree, $transformation->getParameters()->getDegree());
    }

    public function rotatingConfigurationProvider(): array
    {
        return [
            ['r90', 90],
            ['rotate53.3', 53.3],
            ['rotate0.325', 0.325],
            ['rotate-3.5', -3.5],
        ];
    }

    /**
     * @test
     * @param string $configuration
     * @param int $shiftX
     * @param int $shiftY
     * @dataProvider shiftingConfigurationProvider
     */
    public function findAndCreateTransformation_givenShiftingConfiguration_shiftingTransformationWithValidParametersCreated(
        string $configuration,
        int $shiftX,
        int $shiftY
    ): void {
        /** @var ShiftingTransformation $transformation */
        $transformation = $this->transformationCreator->findAndCreateTransformation($configuration);

        $this->assertNotNull($transformation);
        $this->assertInstanceOf(ShiftingTransformation::class, $transformation);
        $this->assertEquals($shiftX, $transformation->getParameters()->getX());
        $this->assertEquals($shiftY, $transformation->getParameters()->getY());
    }

    public function shiftingConfigurationProvider(): array
    {
        return [
            ['sx5y2', 5, 2],
            ['shiftx5y2', 5, 2],
            ['sx-10y-5', -10, -5],
            ['shiftx-10y-5', -10, -5],
            ['sx5', 5, 0],
            ['sy5', 0, 5],
            ['shiftx', 0, 0],
            ['shifty', 0, 0],
        ];
    }
}
