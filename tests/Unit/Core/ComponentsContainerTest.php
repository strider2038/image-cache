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
use Strider2038\ImgCache\Core\ComponentsContainer;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ComponentsContainerTest extends TestCase 
{
    /** @var \Strider2038\ImgCache\Application */
    private $app;

    protected function setUp() 
    {
        $this->app = new class extends Application {
            public function __construct() {
                parent::__construct(['id' => 'test']);
            }
        };
    }
    
    public function testConstruct_ApplicationCreated_InjectionSuccess(): void
    {
        $container = new ComponentsContainer($this->app);
        $this->assertEquals('test', $container->getApp()->getId());
    }

    public function testSet_ComponentCreated_ComponentReturned(): void
    {
        $container = new ComponentsContainer($this->app);
        $component = new class {
            public $id = 'test';
        };
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('testComponent', $component)
        );
        $this->assertTrue(is_object($container->get('testComponent')));
        $this->assertEquals('test', $container->get('testComponent')->id);
    }
    
    public function testSet_NewComponentInCallable_ComponentReturned(): void
    {
        $container = new ComponentsContainer($this->app);
        $callable = function() {
            return new class {
                public $id = 'test';
            };
        };
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('testComponent', $callable)
        );
        $this->assertTrue(is_object($container->get('testComponent')));
        $this->assertEquals('test', $container->get('testComponent')->id);
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Component 'test' already exists
     */
    public function testSet_ComponentAlreadyExists_ExceptionThrown(): void
    {
        $container = new ComponentsContainer($this->app);
        $component = new class {};
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('test', $component)
        );
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('test', $component)
        );
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Component 'test' must be a callable or an object
     */
    public function testSet_ComponentIsString_ExceptionThrown(): void
    {
        $container = new ComponentsContainer($this->app);
        $container->set('test', 'string');
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Component 'test' not found
     */
    public function testGet_ComponentDoesNotExists_ExceptionThrown(): void
    {
        $container = new ComponentsContainer($this->app);
        $container->get('test');
    }
    
    public function testSet_ApplicationInjectedToComponentInlineFactory_ApplicationReturned(): void
    {
        $container = new ComponentsContainer($this->app);
        $test = $this;
        $callable = function($app) use ($test) {
            $test->assertInstanceOf(Application::class, $app);
            return new class {};
        };
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('test', $callable)
        );
    }
    
    /**
     * @expectedException Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage It must be an object and cannot be callable
     */
    public function testGet_InlineFactoryReturnsString_ExceptionThrown(): void
    {
        $container = new ComponentsContainer($this->app);
        $callable = function() {
            return 'notAnObject';
        };
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('testComponent', $callable)
        );
        $this->assertTrue(is_object($container->get('testComponent')));
    }
    
    /**
     * @expectedException Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage It must be an object and cannot be callable
     */
    public function testGet_InlineFactoryReturnsCallable_ExceptionThrown(): void
    {
        $container = new ComponentsContainer($this->app);
        $callable = function() {
            return function() {};
        };
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('testComponent', $callable)
        );
        $this->assertTrue(is_object($container->get('testComponent')));
    }
    
    /**
     * @expectedException Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage It must be an object and cannot be callable
     */
    public function testGet_InlineFactoryReturnsObjectWithInvokeMethod_ExceptionThrown(): void
    {
        $container = new ComponentsContainer($this->app);
        $callable = function() {
            return new class() {
                public function __invoke() {}
            };
        };
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('testComponent', $callable)
        );
        $this->assertTrue(is_object($container->get('testComponent')));
    }
}
