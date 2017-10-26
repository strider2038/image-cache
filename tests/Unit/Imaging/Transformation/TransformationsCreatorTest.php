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
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryFlyweightInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCreator;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationsCreatorTest extends TestCase
{
    /** @var TransformationFactoryFlyweightInterface */
    private $factoryFlyweight;

    protected function setUp()
    {
        $this->factoryFlyweight = \Phake::mock(TransformationFactoryFlyweightInterface::class);
    }

    public function testCreate_GivenValidConfigurationWithFactoryByTwoChars_TransformationIsReturned(): void
    {
        $creator = $this->createTransformationsCreator();
        $this->givenTransformationFactoryFlyweight_FindFactory_Returns('a', $this->givenTransformationFactory());
        $factory = $this->givenTransformationFactory();
        $transformation = $this->givenTransformationFactory_Create_ReturnsTransformation($factory, '12');
        $this->givenTransformationFactoryFlyweight_FindFactory_Returns('ab', $factory);

        $actualTransformation = $creator->create('ab12');

        $this->assertInstanceOf(TransformationInterface::class, $actualTransformation);
        $this->assertSame($transformation, $actualTransformation);
    }

    public function testCreate_GivenValidConfigurationWithFactoryByOneChar_TransformationIsReturned(): void
    {
        $creator = $this->createTransformationsCreator();
        $this->givenTransformationFactoryFlyweight_FindFactory_Returns('ab', $this->givenTransformationFactory());
        $factory = $this->givenTransformationFactory();
        $transformation = $this->givenTransformationFactory_Create_ReturnsTransformation($factory, '12');
        $this->givenTransformationFactoryFlyweight_FindFactory_Returns('a', $factory);

        $actualTransformation = $creator->create('a12');

        $this->assertInstanceOf(TransformationInterface::class, $actualTransformation);
        $this->assertSame($transformation, $actualTransformation);
    }

    public function testCreate_GivenInvalidConfiguration_NullIsReturned(): void
    {
        $creator = $this->createTransformationsCreator();

        $actualTransformation = $creator->create('a12');

        $this->assertNull($actualTransformation);
    }

    private function createTransformationsCreator(): TransformationsCreator
    {
        return new TransformationsCreator($this->factoryFlyweight);
    }

    private function givenTransformationFactory(): TransformationFactoryInterface
    {
        return \Phake::mock(TransformationFactoryInterface::class);
    }

    private function givenTransformationFactory_Create_ReturnsTransformation(
        TransformationFactoryInterface $factory,
        string $configuration
    ): TransformationInterface {
        $transformation = \Phake::mock(TransformationInterface::class);
        \Phake::when($factory)->create($configuration)->thenReturn($transformation);

        return $transformation;
    }

    private function givenTransformationFactoryFlyweight_FindFactory_Returns(
        string $configuration,
        ?TransformationFactoryInterface $factory
    ): void {
        \Phake::when($this->factoryFlyweight)->findFactory($configuration)->thenReturn($factory);
    }
}
