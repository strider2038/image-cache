<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Tests\Support\TransformationsBuilderInterfaceMock;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Transformation\{
    QualityBuilder,
    ResizeBuilder,
    TransformationsFactory,
    TransformationInterface,
    TransformationBuilderInterface
};

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
        $this->assertInstanceOf($instance, $factory->getBuilder($index));
    }

    public function testConstruct_CustomBuildersMap_CustomMapIsUsed(): void
    {
        $factory = new TransformationsFactory([
            'a' => TransformationsBuilderInterfaceMock::class,
        ]);
        $this->assertInstanceOf(
            TransformationBuilderInterface::class, 
            $factory->getBuilder('a')
        );
        $this->assertNull($factory->getBuilder('b'));
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Builders map cannot be empty
     */
    public function testConstruct_EmptyBuildersMap_ExceptionThrown(): void
    {
        new TransformationsFactory([]);
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage does not exist
     */
    public function testConstruct_NotAClassInBuildersMap_ExceptionThrown(): void
    {
        new TransformationsFactory(['a' => 'notAClass']);
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage must implement
     */
    public function testConstruct_IncorrectClassInBuildersMap_ExceptionThrown(): void
    {
        new TransformationsFactory(['a' => self::class]);
    }
    
    /**
     * @expectedException Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Cannot create transformation
     */
    public function testCreate_InvalidConfig_ExceptionThrown(): void
    {
        $factory = new class extends TransformationsFactory {
            public function getBuildersMap(): array
            {
                return [];
            }
        };
        
        $factory->create('anything');
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
        $this->assertInstanceOf($instance, $factory->getBuilder($index));
    }
    
    public function builderIndexProvider(): array
    {
        return [
            ['q', QualityBuilder::class],
            ['s', ResizeBuilder::class],
        ];
    }
    
    public function testGetBuilder_UnknownIndexIsSet_NullIsReturned(): void
    {
        $factory = new TransformationsFactory();
        $this->assertNull($factory->getBuilder('unidentified'));
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
        $this->assertEquals('transformation_a', $transformationA->testId);
        $transformationAB = $factory->create('ab');
        $this->assertEquals('transformation_ab', $transformationAB->testId);
    }
}
