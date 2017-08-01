<?php

namespace Strider2038\ImgCache\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\ControllerInterface;
use Strider2038\ImgCache\Core\RequestInterface;
use Strider2038\ImgCache\Core\ResponseInterface;
use Strider2038\ImgCache\Core\Route;
use Strider2038\ImgCache\Core\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationTest extends TestCase 
{
    const CONTROLLER_ID = 'controller';
    const ACTION_ID = 'action';
    const LOCATION = '/image.jpeg';

    /** @var ContainerInterface */
    private $container;

    protected function setUp()
    {
        $this->container = \Phake::mock(ContainerInterface::class);
    }

    public function testRun_ContainerIsEmpty_1IsReturned(): void
    {
        $this->givenContainer_Get_ThrowsException();
        $application = $this->createApplication();

        $exitCode = $application->run();

        $this->assertEquals(1, $exitCode);
    }

    public function testRun_AllServicesExists_ResponseIsSentAnd0IsReturned(): void
    {
        $request = $this->givenRequest();
        $router = $this->givenRouter();
        $this->givenRouter_GetRoute_ReturnsRoute($router, $request);
        $controller = $this->givenController();
        $response = $this->givenController_RunAction_Returns($controller);
        $application = $this->createApplication();

        $exitCode = $application->run();

        $this->assertEquals(0, $exitCode);
        $this->assertResponse_Send_IsCalledOnce($response);
    }

    private function createApplication(): Application
    {
        $application = new Application($this->container);

        return $application;
    }

    private function givenRequest(): RequestInterface
    {
        $request = \Phake::mock(RequestInterface::class);

        \Phake::when($this->container)->get('request')->thenReturn($request);

        return $request;
    }

    private function givenRouter(): RouterInterface
    {
        $router = \Phake::mock(RouterInterface::class);

        \Phake::when($this->container)->get('router')->thenReturn($router);

        return $router;
    }

    private function givenRouter_GetRoute_ReturnsRoute(RouterInterface $router, RequestInterface $request): void
    {
        $route = \Phake::mock(Route::class);

        \Phake::when($route)->getControllerId()->thenReturn(self::CONTROLLER_ID);
        \Phake::when($route)->getActionId()->thenReturn(self::ACTION_ID);
        \Phake::when($route)->getLocation()->thenReturn(self::LOCATION);

        \Phake::when($router)->getRoute($request)->thenReturn($route);
    }

    private function givenController(): ControllerInterface
    {
        $controller = \Phake::mock(ControllerInterface::class);

        \Phake::when($this->container)->get(self::CONTROLLER_ID)->thenReturn($controller);

        return $controller;
    }

    private function givenController_RunAction_Returns($controller): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);

        \Phake::when($controller)->runAction(self::ACTION_ID, self::LOCATION)->thenReturn($response);

        return $response;
    }

    private function assertResponse_Send_IsCalledOnce($response): void
    {
        \Phake::verify($response, \Phake::times(1))->send();
    }

    private function givenContainer_Get_ThrowsException(): void
    {
        \Phake::when($this->container)->get(\Phake::anyParameters())->thenThrow(new \Exception());
    }
}
