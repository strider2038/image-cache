<?php

namespace Strider2038\ImgCache\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\Component;

/**
 * Description of ComponentTest
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ComponentTest extends TestCase 
{
    public function testConstruct_ApplicationCreated_InjectionSuccess(): void 
    {
        $app = new class extends Application {
            public function __construct() {}
        };
        
        $component = new class($app) extends Component {};
        
        $this->assertInstanceOf(Application::class, $component->getApp());
    }
}
