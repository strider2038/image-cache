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
    Controller, Http\ResponseFactoryInterface, Http\ResponseInterface, SecurityInterface
};
use Strider2038\ImgCache\Enum\HttpStatusCode;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ControllerTest extends TestCase 
{
    private const LOCATION = '/a.jpg';

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var SecurityInterface */
    private $security;

    protected function setUp()
    {
        $this->responseFactory = \Phake::mock(ResponseFactoryInterface::class);
        $this->security = \Phake::mock(SecurityInterface::class);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage does not exists
     */
    public function runAction_actionDoesNotExists_ExceptionThrown(): void
    {
        $controller = new class ($this->responseFactory) extends Controller {};
        
        $controller->runAction('test', self::LOCATION);
    }

    /** @test */
    public function runAction_actionExistsNoSecurityControl_methodExecuted(): void
    {
        $controller = $this->createController();
        
        $result = $controller->runAction('test', self::LOCATION);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertTrue($controller->success);
    }

    /** @test */
    public function runAction_actionIsNotSafeAndNotAuthorized_forbiddenResponseReturned(): void
    {
        $controller = $this->createController($this->security);
        $this->givenSecurity_isAuthorized_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithForbiddenCode();

        $result = $controller->runAction('test', self::LOCATION);

        $this->assertEquals(HttpStatusCode::FORBIDDEN, $result->getStatusCode()->getValue());
        $this->assertFalse($controller->success);
        $this->assertResponseFactory_createMessageResponse_calledWithForbiddenCode();
    }

    /** @test */
    public function runAction_actionIsNotSafeAndIsAuthorized_methodExecuted(): void
    {
        $this->givenSecurity_isAuthorized_returns(true);
        $controller = $this->createController($this->security);
        
        $result = $controller->runAction('test', self::LOCATION);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertTrue($controller->success);
    }

    /** @test */
    public function runAction_actionIsSafeAndSecurityIsDefined_methodExecuted(): void
    {
        $controller = new class($this->responseFactory, $this->security) extends Controller
        {
            public $success = false;

            protected function getSafeActions(): array
            {
                return ['test'];
            }

            public function actionTest()
            {
                $this->success = true;
                return \Phake::mock(ResponseInterface::class);
            }
        };

        $this->assertFalse($controller->success);
        $result = $controller->runAction('test', self::LOCATION);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertTrue($controller->success);
    }

    private function createController(SecurityInterface $security = null): Controller
    {
        $controller = new class($this->responseFactory, $security) extends Controller
        {
            public $success = false;

            public function actionTest()
            {
                $this->success = true;
                return \Phake::mock(ResponseInterface::class);
            }
        };

        return $controller;
    }

    private function givenSecurity_isAuthorized_returns(bool $value): void
    {
        \Phake::when($this->security)->isAuthorized()->thenReturn($value);
    }

    private function givenResponseFactory_createMessageResponse_returnsResponseWithForbiddenCode(): void
    {
        $response = \Phake::mock(ResponseInterface::class);

        \Phake::when($response)
            ->getStatusCode()
            ->thenReturn(new HttpStatusCode(HttpStatusCode::FORBIDDEN));

        \Phake::when($this->responseFactory)
            ->createMessageResponse(\Phake::anyParameters())
            ->thenReturn($response);
    }

    private function assertResponseFactory_createMessageResponse_calledWithForbiddenCode(): void
    {
        \Phake::verify($this->responseFactory, \Phake::times(1))
            ->createMessageResponse(\Phake::capture($httpStatusCode));

        $this->assertEquals(HttpStatusCode::FORBIDDEN, $httpStatusCode->getValue());
    }
}
