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
use Strider2038\ImgCache\Imaging\Transformation\ResizeTransformationFactory;
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryFlyweight;
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryInterface;
use Strider2038\ImgCache\Tests\Support\TransformationsFactoryInterfaceMock;

class TransformationFactoryFlyweightTest extends TestCase
{
    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage does not exist
     */
    public function construct_notAClassInBuildersMap_exceptionThrown(): void
    {
        new TransformationFactoryFlyweight(['a' => 'notAClass']);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage must implement
     */
    public function construct_incorrectClassInBuildersMap_exceptionThrown(): void
    {
        new TransformationFactoryFlyweight(['a' => self::class]);
    }

    /**
     * @test
     * @param string $index
     * @param string $instance
     * @dataProvider factoryIndexProvider
     */
    public function findFactory_givenDefaultFactoriesMapAndIndex_factoryIsReturned(
        string $index,
        string $instance
    ): void {
        $flyweight = new TransformationFactoryFlyweight();

        $factory = $flyweight->findFactory($index);

        $this->assertInstanceOf($instance, $factory);
    }

    /** @test */
    public function findFactory_givenCustomBuildersMapAndValidIndex_factoryIsReturned(): void
    {
        $flyweight = new TransformationFactoryFlyweight([
            'a' => TransformationsFactoryInterfaceMock::class,
        ]);

        $factory = $flyweight->findFactory('a');

        $this->assertInstanceOf(TransformationFactoryInterface::class, $factory);
    }

    /** @test */
    public function findFactory_givenCustomBuildersMapAndInvalidIndex_nullIsReturned(): void
    {
        $flyweight = new TransformationFactoryFlyweight([
            'a' => TransformationsFactoryInterfaceMock::class,
        ]);

        $factory = $flyweight->findFactory('b');

        $this->assertNull($factory);
    }

    public function factoryIndexProvider(): array
    {
        return [
            ['s', ResizeTransformationFactory::class],
        ];
    }
}
