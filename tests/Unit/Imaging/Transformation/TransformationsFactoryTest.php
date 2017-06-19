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
use Strider2038\ImgCache\Application;
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
    /** @var \Strider2038\ImgCache\Application */
    private $app;
    
    public function setUp()
    {
        $this->app = new class extends Application {
            public function __construct() {}
        };
    }
    
    /**
     * @expectedException Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Cannot create transformation
     */
    public function testCreate_InvalidConfig_ExceptionThrown(): void
    {
        $factory = new class($this->app) extends TransformationsFactory {
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
    public function testGetBuildersMap_NoParams_BuildersReturned($index, $instance): void
    {
        $factory = new TransformationsFactory($this->app);
        $builders = $factory->getBuildersMap();
        $this->assertArrayHasKey($index, $builders);
        $this->assertInstanceOf($instance, new $builders[$index]);
    }
    
    /**
     * @dataProvider builderIndexProvider
     */
    public function testGetBuilder_IndexIsSet_InstanceIsReturned($index, $instance): void
    {
        $factory = new TransformationsFactory($this->app);
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
        $factory = new TransformationsFactory($this->app);
        $this->assertNull($factory->getBuilder('unidentified'));
    }
    
    public function testCreate_ConfigIsSet_TransformationIsReturned(): void
    {
        $factory = new class($this->app) extends TransformationsFactory {
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
