<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Transformation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Transformation\ResizeTransformation;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCreator;
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryMap;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationCreatorTest extends TestCase
{
    private const INVALID_CONFIGURATION = 'configuration';
    private const CUSTOM_CONFIGURATION = 'transformation-id400x200';
    private const CUSTOM_CONFIGURATION_VALUE = '400x200';
    private const SIZE_CONFIGURATION = 'size400x200';

    /** @test */
    public function createTransformation_givenConfigurationAndFactoryNotFound_nullReturned(): void
    {
        $factoryMap = new TransformationFactoryMap();
        $creator = new TransformationCreator($factoryMap);

        $transformation = $creator->createTransformation(self::INVALID_CONFIGURATION);

        $this->assertNull($transformation);
    }

    /** @test */
    public function createTransformation_givenCustomFactoryMapConfigurationAndFactoryFound_transformationCreatedAndReturned(): void
    {
        $factory = \Phake::mock(TransformationFactoryInterface::class);
        $factoryMap = new TransformationFactoryMap([
            '/^transformation-id(.*)$/' => $factory
        ]);
        $creator = new TransformationCreator($factoryMap);
        $expectedTransformation = $this->givenTransformationFactory_createTransformation_returnsTransformation($factory);

        $transformation = $creator->createTransformation(self::CUSTOM_CONFIGURATION);

        $this->assertNotNull($transformation);
        $this->assertTransformationFactory_createTransformation_isCalledOnceWithValue($factory, self::CUSTOM_CONFIGURATION_VALUE);
        $this->assertSame($expectedTransformation, $transformation);
    }

    /** @test */
    public function createTransformation_givenDefaultFactoryMapConfigurationAndFactoryFound_transformationCreatedAndReturned(): void
    {
        $creator = new TransformationCreator();

        $transformation = $creator->createTransformation(self::SIZE_CONFIGURATION);

        $this->assertInstanceOf(ResizeTransformation::class, $transformation);
    }

    private function givenTransformationFactory_createTransformation_returnsTransformation(
        TransformationFactoryInterface $factory
    ): TransformationInterface {
        $transformation = \Phake::mock(TransformationInterface::class);
        \Phake::when($factory)->createTransformation(\Phake::anyParameters())->thenReturn($transformation);

        return $transformation;
    }

    private function assertTransformationFactory_createTransformation_isCalledOnceWithValue(
        TransformationFactoryInterface $factory,
        string $value
    ): void {
        \Phake::verify($factory, \Phake::times(1))->createTransformation($value);
    }
}
