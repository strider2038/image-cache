<?php

namespace Strider2038\ImgCache\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\ControllerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\ResponseInterface;
use Strider2038\ImgCache\Core\Route;
use Strider2038\ImgCache\Core\RouterInterface;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationTest extends TestCase 
{
    use ProviderTrait, LoggerTrait;

    private const LOGGER_ID = 'logger';
    private const ROUTER_ID = 'router';
    private const REQUEST_ID = 'request';
    private const CONFIG_DEBUG = 'app.debug';
    private const CONTROLLER_ID = 'controller';
    private const ACTION_ID = 'action';
    private const LOCATION = '/image.jpeg';
    private const EXCEPTION_MESSAGE = 'application exception message';

    /** @var ContainerInterface */
    private $container;

    protected function setUp()
    {
        $this->container = \Phake::mock(ContainerInterface::class);
    }

    /** @test */
    public function run_containerIsEmpty_1IsReturned(): void
    {
        $this->givenContainer_Get_ThrowsException();
        $logger = $this->givenContainer_Get_ReturnsLogger();
        $application = $this->createApplication();

        $exitCode = $application->run();

        $this->assertEquals(1, $exitCode);
        $this->assertLogger_Error_IsCalledOnce($logger);
    }

    /** @test */
    public function run_allServicesExists_responseIsSentAnd0IsReturned(): void
    {
        $request = $this->givenContainer_Get_ReturnsRequest();
        $router = $this->givenContainer_Get_ReturnsRouter();
        $this->givenRouter_GetRoute_ReturnsRoute($router, $request);
        $controller = $this->givenController();
        $response = $this->givenController_RunAction_Returns($controller);
        $application = $this->createApplication();

        $exitCode = $application->run();

        $this->assertEquals(0, $exitCode);
        $this->assertResponse_Send_IsCalledOnce($response);
    }

    /**
     * @test
     * @param bool $value
     * @dataProvider boolValuesProvider
     */
    public function isDebugMode_containerHasDebugParameter_boolIsReturned(bool $value): void
    {
        $application = $this->createApplication();
        $this->givenContainerHasParameterDebug($value);

        $isDebugMode = $application->isDebugMode();

        $this->assertEquals($value, $isDebugMode);
    }

    private function createApplication(): Application
    {
        $application = new Application($this->container);

        return $application;
    }

    private function givenContainer_Get_ReturnsRequest(): RequestInterface
    {
        $request = \Phake::mock(RequestInterface::class);
        \Phake::when($this->container)->get(self::REQUEST_ID)->thenReturn($request);

        return $request;
    }

    private function givenContainer_Get_ReturnsRouter(): RouterInterface
    {
        $router = \Phake::mock(RouterInterface::class);

        \Phake::when($this->container)->get(self::ROUTER_ID)->thenReturn($router);

        return $router;
    }

    private function givenContainer_Get_ReturnsLogger(): LoggerInterface
    {
        $logger = $this->givenLogger();

        \Phake::when($this->container)->has(self::LOGGER_ID)->thenReturn(true);
        \Phake::when($this->container)->get(self::LOGGER_ID)->thenReturn($logger);

        return $logger;
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
        \Phake::when($this->container)
            ->get(\Phake::anyParameters())
            ->thenThrow(new \Exception(self::EXCEPTION_MESSAGE));
    }

    private function givenContainerHasParameterDebug(bool $value): void
    {
        \Phake::when($this->container)->hasParameter(self::CONFIG_DEBUG)->thenReturn(true);
        \Phake::when($this->container)->getParameter(self::CONFIG_DEBUG)->thenReturn($value);
    }
}
