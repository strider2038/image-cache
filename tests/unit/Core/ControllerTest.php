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
    Controller,
    RequestInterface,
    ResponseInterface
};
use Strider2038\ImgCache\Response\ForbiddenResponse;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ControllerTest extends TestCase 
{

    /**
     * @expectedException \Strider2038\ImgCache\Exception\RuntimeException
     * @expectedExceptionMessage does not exists
     */
    public function testRunAction_ActionDoesNotExists_ExceptionThrown() 
    {
        $app = new class extends Application {
            public function __construct() {}
        };
        
        $request = new class implements RequestInterface {
            public function getMethod(): string
            {
                return 'getMethodResult';
            }
            public function getHeader(string $key): ?string
            {
                return 'getHeaderResult';
            }
        };
        
        $controller = new class($app) extends Controller {};
        
        $controller->runAction('test', $request);
    }
    
    public function testRunAction_ActionExists_MethodExecuted() 
    {
        $app = new class extends Application {
            public function __construct() {}
        };
        
        $request = new class implements RequestInterface {
            public function getMethod(): string
            {
                return 'getMethodResult';
            }
            public function getHeader(string $key): ?string
            {
                return 'getHeaderResult';
            }
        };
        
        $controller = new class($app) extends Controller {
            public $success = false;
            public function actionTest()
            {
                $this->success = true;
                return new class implements ResponseInterface {
                    public function send(): void {}
                };
            }
            protected function getInsecureActions(): array
            {
                return ['test'];
            }
        };
        
        $this->assertInstanceOf(
            ResponseInterface::class, 
            $controller->runAction('test', $request)
        );
        $this->assertTrue($controller->success);
    }

    public function testRunAction_ActionIsNotSecuredAndNotAuthorized_ForbiddenResponseReturned()
    {
        $app = new class extends Application {
            public function __construct() {}
            public $security;
        };
        
        $app->security = new class {
            public function isAuthorized() {
                return false;
            }
        };
        
        $request = new class implements RequestInterface {
            public function getMethod(): string
            {
                return 'getMethodResult';
            }
            public function getHeader(string $key): ?string
            {
                return 'getHeaderResult';
            }
        };
        
        $controller = new class($app) extends Controller {
            public $success = false;
            public function actionTest()
            {
                $this->success = true;
                return new class implements ResponseInterface {
                    public function send(): void {}
                };
            }
        };
        
        $this->assertInstanceOf(
            ForbiddenResponse::class, 
            $controller->runAction('test', $request)
        );
        $this->assertFalse($controller->success);
    }
    
    public function testRunAction_ActionIsNotSecuredAndIsAuthorized_MethodExecuted()
    {
        $app = new class extends Application {
            public function __construct() {}
            public $security;
        };
        
        $app->security = new class {
            public function isAuthorized() {
                return true;
            }
        };
        
        $request = new class implements RequestInterface {
            public function getMethod(): string
            {
                return 'getMethodResult';
            }
            public function getHeader(string $key): ?string
            {
                return 'getHeaderResult';
            }
        };
        
        $controller = new class($app) extends Controller {
            public $success = false;
            public function actionTest()
            {
                $this->success = true;
                return new class implements ResponseInterface {
                    public function send(): void {}
                };
            }
        };
        
        $this->assertInstanceOf(
            ResponseInterface::class, 
            $controller->runAction('test', $request)
        );
        $this->assertTrue($controller->success);
    }
}
