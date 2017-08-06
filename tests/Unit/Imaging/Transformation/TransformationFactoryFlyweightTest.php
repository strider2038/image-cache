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

use Strider2038\ImgCache\Imaging\Transformation\ResizeFactory;
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryFlyweight;
use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Transformation\TransformationFactoryInterface;
use Strider2038\ImgCache\Tests\Support\TransformationsFactoryInterfaceMock;

class TransformationFactoryFlyweightTest extends TestCase
{
    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage does not exist
     */
    public function testConstruct_NotAClassInBuildersMap_ExceptionThrown(): void
    {
        new TransformationFactoryFlyweight(['a' => 'notAClass']);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage must implement
     */
    public function testConstruct_IncorrectClassInBuildersMap_ExceptionThrown(): void
    {
        new TransformationFactoryFlyweight(['a' => self::class]);
    }

    /**
     * @param string $index
     * @param string $instance
     * @dataProvider factoryIndexProvider
     */
    public function testFindFactory_GivenDefaultFactoriesMapAndIndex_FactoryIsReturned(
        string $index,
        string $instance
    ): void {
        $flyweight = new TransformationFactoryFlyweight();

        $factory = $flyweight->findFactory($index);

        $this->assertInstanceOf($instance, $factory);
    }

    public function testFindFactory_GivenCustomBuildersMapAndValidIndex_FactoryIsReturned(): void
    {
        $flyweight = new TransformationFactoryFlyweight([
            'a' => TransformationsFactoryInterfaceMock::class,
        ]);

        $factory = $flyweight->findFactory('a');

        $this->assertInstanceOf(TransformationFactoryInterface::class, $factory);
    }

    public function testFindFactory_GivenCustomBuildersMapAndInvalidIndex_NullIsReturned(): void
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
            ['s', ResizeFactory::class],
        ];
    }
}
