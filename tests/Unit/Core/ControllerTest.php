<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\{
    Controller, ResponseInterface, SecurityInterface
};
use Strider2038\ImgCache\Response\ForbiddenResponse;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ControllerTest extends TestCase 
{
    const LOCATION = '/a.jpg';
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionMessage does not exists
     */
    public function testRunAction_ActionDoesNotExists_ExceptionThrown(): void
    {
        $controller = new class extends Controller {};
        
        $controller->runAction('test', self::LOCATION);
    }
    
    public function testRunAction_ActionExistsNoSecurityControl_MethodExecuted(): void
    {
        $controller = $this->buildController();
        
        $this->assertFalse($controller->success);
        $result = $controller->runAction('test', self::LOCATION);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertTrue($controller->success);
    }

    public function testRunAction_ActionIsNotSafeAndNotAuthorized_ForbiddenResponseReturned(): void
    {
        $security = \Phake::mock(SecurityInterface::class);
        \Phake::when($security)->isAuthorized()->thenReturn(false);
        $controller = $this->buildController($security);

        $this->assertFalse($controller->success);
        $result = $controller->runAction('test', self::LOCATION);

        $this->assertInstanceOf(ForbiddenResponse::class, $result);
        $this->assertFalse($controller->success);
    }
    
    public function testRunAction_ActionIsNotSafeAndIsAuthorized_MethodExecuted(): void
    {
        $security = \Phake::mock(SecurityInterface::class);
        \Phake::when($security)->isAuthorized()->thenReturn(true);
        $controller = $this->buildController($security);
        
        $this->assertFalse($controller->success);
        $result = $controller->runAction('test', self::LOCATION);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertTrue($controller->success);
    }

    public function testRunAction_ActionIsSafeAndSecurityIsDefined_MethodExecuted(): void
    {
        $security = \Phake::mock(SecurityInterface::class);
        $controller = new class($security) extends Controller
        {
            public $success = false;

            protected function getSafeActions(): array
            {
                return ['test'];
            }

            public function actionTest()
            {
                $this->success = true;
                return new class implements ResponseInterface
                {
                    public function send(): void
                    {
                    }
                };
            }
        };

        $this->assertFalse($controller->success);
        $result = $controller->runAction('test', self::LOCATION);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertTrue($controller->success);
    }

    private function buildController(SecurityInterface $security = null): Controller
    {
        $controller = new class($security) extends Controller
        {
            public $success = false;

            public function actionTest()
            {
                $this->success = true;
                return new class implements ResponseInterface
                {
                    public function send(): void
                    {
                    }
                };
            }
        };

        return $controller;
    }
}
