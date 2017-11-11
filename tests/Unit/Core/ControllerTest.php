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
    ActionInterface, Controller, Http\ResponseFactoryInterface, Http\ResponseInterface, SecurityInterface
};
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ControllerTest extends TestCase 
{
    private const LOCATION = '/a.jpg';
    private const ACTION_ID = 'test';

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var SecurityInterface */
    private $security;

    protected function setUp()
    {
        $this->responseFactory = \Phake::mock(ResponseFactoryInterface::class);
        $this->security = \Phake::mock(SecurityInterface::class);
    }

    /** @test */
    public function runAction_actionExistsNoSecurityControl_methodExecuted(): void
    {
        $controller = $this->createController();
        $action = $this->givenController_createAction_returnsAction($controller);
        $expectedResponse = $this->givenAction_run_returnsResponse($action);

        $response = $controller->runAction(self::ACTION_ID, self::LOCATION);

        $this->assertController_createAction_isCalledOnceWithActionAndLocation($controller);
        $this->assertAction_run_isCalledOnce($action);
        $this->assertSame($expectedResponse, $response);
    }

    /** @test */
    public function runAction_actionIsNotSafeAndNotAuthorized_forbiddenResponseReturned(): void
    {
        $controller = $this->createController($this->security);
        $this->givenSecurity_isAuthorized_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithForbiddenCode();

        $result = $controller->runAction(self::ACTION_ID, self::LOCATION);

        $this->assertEquals(HttpStatusCodeEnum::FORBIDDEN, $result->getStatusCode()->getValue());
        $this->assertResponseFactory_createMessageResponse_calledWithForbiddenCode();
        $this->assertController_createAction_isNotCalled($controller);
    }

    /** @test */
    public function runAction_actionIsNotSafeAndIsAuthorized_methodExecuted(): void
    {
        $controller = $this->createController($this->security);
        $this->givenSecurity_isAuthorized_returns(true);
        $action = $this->givenController_createAction_returnsAction($controller);
        $expectedResponse = $this->givenAction_run_returnsResponse($action);

        $response = $controller->runAction(self::ACTION_ID, self::LOCATION);

        $this->assertController_createAction_isCalledOnceWithActionAndLocation($controller);
        $this->assertAction_run_isCalledOnce($action);
        $this->assertSame($expectedResponse, $response);
    }

    /** @test */
    public function runAction_actionIsSafeAndSecurityIsDefined_methodExecuted(): void
    {
        $controller = $this->createController($this->security);
        $this->givenController_getSafeActions_returnsArrayWithActionId($controller);
        $action = $this->givenController_createAction_returnsAction($controller);
        $expectedResponse = $this->givenAction_run_returnsResponse($action);

        $response = $controller->runAction(self::ACTION_ID, self::LOCATION);

        $this->assertController_createAction_isCalledOnceWithActionAndLocation($controller);
        $this->assertAction_run_isCalledOnce($action);
        $this->assertSame($expectedResponse, $response);
    }

    private function createController(SecurityInterface $security = null): Controller
    {
        return \Phake::partialMock(Controller::class, $this->responseFactory, $security);
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
            ->thenReturn(new HttpStatusCodeEnum(HttpStatusCodeEnum::FORBIDDEN));

        \Phake::when($this->responseFactory)
            ->createMessageResponse(\Phake::anyParameters())
            ->thenReturn($response);
    }

    private function assertResponseFactory_createMessageResponse_calledWithForbiddenCode(): void
    {
        \Phake::verify($this->responseFactory, \Phake::times(1))
            ->createMessageResponse(\Phake::capture($httpStatusCode));

        $this->assertEquals(HttpStatusCodeEnum::FORBIDDEN, $httpStatusCode->getValue());
    }

    private function givenController_createAction_returnsAction(Controller $controller): ActionInterface
    {
        $action = \Phake::mock(ActionInterface::class);
        \Phake::when($controller)->createAction(\Phake::anyParameters())->thenReturn($action);

        return $action;
    }

    private function assertController_createAction_isCalledOnceWithActionAndLocation(Controller $controller): void
    {
        \Phake::verify($controller, \Phake::times(1))->createAction(self::ACTION_ID, self::LOCATION);
    }

    private function assertController_createAction_isNotCalled(Controller $controller): void
    {
        \Phake::verify($controller, \Phake::times(0))->createAction(\Phake::anyParameters());
    }

    private function assertAction_run_isCalledOnce(ActionInterface $action): void
    {
        \Phake::verify($action, \Phake::times(1))->run();
    }

    private function givenAction_run_returnsResponse(ActionInterface $action): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($action)->run()->thenReturn($response);

        return $response;
    }

    private function givenController_getSafeActions_returnsArrayWithActionId(Controller $controller): void
    {
        \Phake::when($controller)->getSafeActions()->thenReturn([self::ACTION_ID]);
    }
}
