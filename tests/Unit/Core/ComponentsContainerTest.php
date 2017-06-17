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
use Strider2038\ImgCache\Core\{ 
    Component,
    ComponentsContainer
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ComponentsContainerTest extends TestCase 
{
    /** @var \Strider2038\ImgCache\Application */
    private $app;

    public function setUp() 
    {
        $this->app = new class extends Application {
            public function __construct() {
                parent::__construct(['id' => 'test']);
            }
        };
    }
    
    public function testConstruct_ApplicationCreated_InjectionSuccess() 
    {
        $container = new ComponentsContainer($this->app);
        $this->assertEquals('test', $container->getApp()->getId());
    }

    public function testSet_ComponentCreated_ComponentReturned() 
    {
        $container = new ComponentsContainer($this->app);
        $component = new class($this->app) extends Component {};
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('testComponent', $component)
        );
        $this->assertInstanceOf(
            Component::class, 
            $container->get('testComponent')
        );
    }
    
    public function testSet_NewComponentInCallable_ComponentReturned() 
    {
        $container = new ComponentsContainer($this->app);
        $callable = function($app) {
            return new class($app) extends Component {};
        };
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('testComponent', $callable)
        );
        $this->assertInstanceOf(
            Component::class, 
            $container->get('testComponent')
        );
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionMessage Component 'test' is already exists
     */
    public function testSet_ComponentAlreadyExists_ExceptionThrown() 
    {
        $container = new ComponentsContainer($this->app);
        $component = new class($this->app) extends Component {};
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
     * @expectedExceptionMessage Component 'test' must be a callable or an instance of
     */
    public function testSet_ComponentIsString_ExceptionThrown() 
    {
        $container = new ComponentsContainer($this->app);
        $container->set('test', 'string');
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionMessage Component 'test' not found
     */
    public function testGet_ComponentDoesNotExists_ExceptionThrown() 
    {
        $container = new ComponentsContainer($this->app);
        $container->get('test');
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionMessage Component 'test' must be instance
     */
    public function testGet_ComponentIsNotInstanceOfComponent_ExceptionThrown() 
    {
        $container = new ComponentsContainer($this->app);
        $component = function() {
            return new class {};
        };
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('test', $component)
        );
        $container->get('test');
    }
    
    public function testSet_ApplicationInjectedToComponent_ApplicationReturned() 
    {
        $container = new ComponentsContainer($this->app);
        $component = function($app) {
            return new class($app) extends Component {};
        };
        $this->assertInstanceOf(
            ComponentsContainer::class, 
            $container->set('test', $component)
        );
        $returnedComponent = $container->get('test');
        $this->assertInstanceOf(Component::class, $returnedComponent);
        $this->assertInstanceOf(Application::class, $returnedComponent->getApp());
    }
}
