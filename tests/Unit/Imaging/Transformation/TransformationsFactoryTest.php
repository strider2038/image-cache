<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging\Transformation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Transformation\ResizeBuilder;
use Strider2038\ImgCache\Imaging\Transformation\TransformationBuilderInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsFactory;
use Strider2038\ImgCache\Tests\Support\TransformationsBuilderInterfaceMock;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationsFactoryTest extends TestCase
{
    /**
     * @dataProvider builderIndexProvider
     */
    public function testConstruct_NoBuildersMap_DefaultMapIsUsed(string $index, string $instance): void
    {
        $factory = new TransformationsFactory();

        $builder = $factory->getBuilder($index);

        $this->assertInstanceOf($instance, $builder);
    }

    public function testConstruct_CustomBuildersMap_CustomMapIsUsed(): void
    {
        $factory = new TransformationsFactory([
            'a' => TransformationsBuilderInterfaceMock::class,
        ]);

        $builderA = $factory->getBuilder('a');
        $builderS = $factory->getBuilder('s');

        $this->assertInstanceOf(TransformationBuilderInterface::class, $builderA);
        $this->assertNull($builderS);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Builders map cannot be empty
     */
    public function testConstruct_EmptyBuildersMap_ExceptionThrown(): void
    {
        new TransformationsFactory([]);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage does not exist
     */
    public function testConstruct_NotAClassInBuildersMap_ExceptionThrown(): void
    {
        new TransformationsFactory(['a' => 'notAClass']);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage must implement
     */
    public function testConstruct_IncorrectClassInBuildersMap_ExceptionThrown(): void
    {
        new TransformationsFactory(['a' => self::class]);
    }

    public function testCreate_InvalidConfig_NullIsReturned(): void
    {
        $factory = new class extends TransformationsFactory {
            public static function getDefaultBuildersMap(): array
            {
                return [];
            }
        };

        $transformation = $factory->create('anything');

        $this->assertNull($transformation);
    }

    /**
     * @dataProvider builderIndexProvider
     */
    public function testGetDefaultBuildersMap_NoParams_BuildersReturned(string $index, string $instance): void
    {
        $builders = TransformationsFactory::getDefaultBuildersMap();

        $this->assertArrayHasKey($index, $builders);
        $this->assertInstanceOf($instance, new $builders[$index]);
    }

    /**
     * @dataProvider builderIndexProvider
     */
    public function testGetBuilder_IndexIsSet_InstanceIsReturned(string $index, string $instance): void
    {
        $factory = new TransformationsFactory();

        $builder = $factory->getBuilder($index);

        $this->assertInstanceOf($instance, $builder);
    }

    public function builderIndexProvider(): array
    {
        return [
            ['s', ResizeBuilder::class],
        ];
    }

    public function testGetBuilder_UnknownIndexIsSet_NullIsReturned(): void
    {
        $factory = new TransformationsFactory();

        $builder = $factory->getBuilder('unidentified');

        $this->assertNull($builder);
    }

    public function testCreate_ConfigIsSet_TransformationIsReturned(): void
    {
        $factory = new class extends TransformationsFactory {
            public function getBuilder(string $index): ?TransformationBuilderInterface
            {
                switch ($index) {
                    case 'a':
                        return new class implements TransformationBuilderInterface {
                            public function build(string $config): TransformationInterface
                            {
                                return new class implements TransformationInterface {
                                    public $testId = 'transformation_a';

                                    public function apply(ProcessingImageInterface $image): void {}
                                };
                            }
                        };
                    case 'ab':
                        return new class implements TransformationBuilderInterface {
                            public function build(string $config): TransformationInterface
                            {
                                return new class implements TransformationInterface {
                                    public $testId = 'transformation_ab';

                                    public function apply(ProcessingImageInterface $image): void {}
                                };
                            }
                        };
                }
                return null;
            }
        };

        $transformationA = $factory->create('a');
        $transformationAB = $factory->create('ab');
        $transformationA1 = $factory->create('a1');

        $this->assertEquals('transformation_a', $transformationA->testId);
        $this->assertEquals('transformation_ab', $transformationAB->testId);
        $this->assertEquals('transformation_a', $transformationA1->testId);
    }
}
