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
use Strider2038\ImgCache\Core\{
    Controller,
    RequestInterface,
    ResponseInterface
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ControllerTest extends TestCase 
{

    /**
     * @expectedException \Strider2038\ImgCache\Exception\RuntimeException
     * @expectedExceptionMessage does not exists
     */
    public function testRunActionDoesNotExists() 
    {
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
        
        $controller = new class extends Controller {};
        
        $controller->runAction('test', $request);
    }
    
    public function testRunActionSuccess() 
    {
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
        
        $controller = new class extends Controller {
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
