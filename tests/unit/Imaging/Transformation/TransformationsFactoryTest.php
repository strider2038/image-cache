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
}
