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
use Strider2038\ImgCache\Imaging\Image;
use Strider2038\ImgCache\Imaging\Transformation\{
    Quality,
    Resize,
    TransformationsFactory,
    TransformationInterface
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
    
    public function testConstruct_CustomConstructorsSet_DefaultConstructorsReplacedInCreate(): void
    {
        $customConstructors = [
            'a' => function() {
                return new class implements TransformationInterface {
                    public $tid = 'custom_a';
                    public function apply(Image $image): void {}
                };
            },
            'b' => function() {
                return new class implements TransformationInterface {
                    public $tid = 'custom_b';
                    public function apply(Image $image): void {}
                };
            },
        ];
        
        $factory = new class($this->app, $customConstructors) extends TransformationsFactory {
            public function getDefaultConstructors(): array
            {
                return [
                    'b' => function() {
                        return new class implements TransformationInterface {
                            public $tid = 'default_b';
                            public function apply(Image $image): void {}
                        };
                    },
                    'c' => function() {
                        return new class implements TransformationInterface {
                            public $tid = 'default_c';
                            public function apply(Image $image): void {}
                        };
                    },
                ];
            }
        };
        
        $this->assertEquals('custom_a', $factory->create('a')->tid);
        $this->assertEquals('custom_b', $factory->create('b')->tid);
        $this->assertEquals('default_c', $factory->create('c')->tid);
    }

    /**
     * @expectedException Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Cannot create transformation
     */
    public function testCreate_InvalidConfig_ExceptionThrown(): void
    {
        $factory = new class($this->app) extends TransformationsFactory {
            public function getDefaultConstructors(): array
            {
                return [];
            }
        };
        
        $factory->create('anything');
    }
    
    public function testGetDefaultConstructors_Default_CallbacksReturned(): void
    {
        $factory = new TransformationsFactory($this->app);
        $constructors = $factory->getDefaultConstructors();
        $this->assertArrayHasKey('q', $constructors);
        $this->assertArrayHasKey('s', $constructors);
        $this->assertInstanceOf(Quality::class, $constructors['q'](50));
        $this->assertInstanceOf(Resize::class, $constructors['s']('200x200f'));
    }
    
    public function testCreateQuality_ValidConfig_ClassIsConstructed(): void
    {
        $factory = new TransformationsFactory($this->app);
        $constructors = $factory->getDefaultConstructors();
        foreach ([20, 50, 100] as $value) {
            $this->assertInstanceOf(Quality::class, $constructors['q']($value));
        }
    }
    
    /**
     * @dataProvider qualityInvalidConfigProvider
     * @expectedException Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid config for quality transformation
     */
    public function testCreateQuality_InvalidConfig_ClassIsConstructed($config): void
    {
        $factory = new TransformationsFactory($this->app);
        $factory->getDefaultConstructors()['q']($config);
    }
    
    public function qualityInvalidConfigProvider(): array
    {
        return [['20h'], ['abc'], ['']];
    }
    
    /**
     * @dataProvider resizeConfigProvider
     */
    public function testCreateResize_ValidConfig_ClassIsConstructed($config, $width, $height, $mode): void
    {
        $factory = new TransformationsFactory($this->app);
        $constructors = $factory->getDefaultConstructors();
        $resize = $constructors['s']($config);
        $this->assertInstanceOf(Resize::class, $resize);
        $this->assertEquals($width, $resize->getWidth());
        $this->assertEquals($height, $resize->getHeigth());
        $this->assertEquals($mode, $resize->getMode());
    }
    
    public function resizeConfigProvider(): array
    {
        return [
            ['100x100f', 100, 100, Resize::MODE_FIT_IN],
            ['500x200s', 500, 200, Resize::MODE_STRETCH],
            ['50x1000w', 50, 1000, Resize::MODE_PRESERVE_WIDTH],
            ['300x200h', 300, 200, Resize::MODE_PRESERVE_HEIGHT],
            ['400X250H', 400, 250, Resize::MODE_PRESERVE_HEIGHT],
            ['200x300', 200, 300, Resize::MODE_STRETCH],
            ['200f', 200, 200, Resize::MODE_FIT_IN],
            ['150', 150, 150, Resize::MODE_STRETCH],
        ];
    }
    
    /**
     * @dataProvider resizeInvalidConfigProvider
     * @expectedException Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid config for resize transformation
     */
    public function testCreateResize_InvalidConfig_ExceptionThrown($config): void
    {
        $factory = new TransformationsFactory($this->app);
        $constructors = $factory->getDefaultConstructors();
        $resize = $constructors['s']($config);
    }
    
    public function resizeInvalidConfigProvider(): array
    {
        return [
            ['1500k'],
            ['100x15i'],
            ['100x156sp'],
            ['100x'],
        ];
    }
}
