<?php

namespace Strider2038\ImgCache\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\ControllerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
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
    private const RESPONSE_FACTORY_ID = 'responseFactory';
    private const RESPONSE_SENDER_ID = 'responseSender';
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

    /**
     * @test
     * @runInSeparateProcess
     * @group separate
     */
    public function construct_containerIsEmpty_serverErrorIsReturned(): void
    {
        $this->givenContainer_get_throwsException();

        $application = $this->createApplication();

        $this->assertEquals(500, http_response_code());
        $this->assertEquals(1, $application->run());
    }

    /** @test */
    public function run_allServicesExists_responseIsSentAnd0IsReturned(): void
    {
        $responseSender = $this->givenContainer_get_returnsResponseSender();
        $request = $this->givenContainer_get_returnsRequest();
        $router = $this->givenContainer_get_returnsRouter();
        $this->givenRouter_getRoute_returnsRoute($router, $request);
        $controller = $this->givenController();
        $response = $this->givenController_runAction_returns($controller);
        $application = $this->createApplication();

        $exitCode = $application->run();

        $this->assertEquals(0, $exitCode);
        $this->assertResponseSender_send_isCalledOnce($responseSender, $response);
    }

    /** @test */
    public function run_allServicesExistsAndRouterThrowsException_errorResponseIsSent(): void
    {
        $logger = $this->givenContainer_get_returnsLogger();
        $responseSender = $this->givenContainer_get_returnsResponseSender();
        $responseFactory = $this->givenContainer_get_returnsResponseFactory();
        $response = $this->givenResponseFactory_createExceptionResponse_returnsResponse($responseFactory);
        $request = $this->givenContainer_get_returnsRequest();
        $router = $this->givenContainer_get_returnsRouter();
        $this->givenRouter_getRoute_throwsException($router);
        $application = $this->createApplication();

        $exitCode = $application->run();

        $this->assertEquals(1, $exitCode);
        $this->assertLogger_error_isCalledOnce($logger);
        $this->assertResponseSender_send_isCalledOnce($responseSender, $response);
    }

    /** @test */
    public function onShutdown_givenLoggerAndError_loggerCriticalIsCalled(): void
    {
        $logger = \Phake::mock(LoggerInterface::class);
        $error = ['message' => 'error'];

        Application::onShutdown($logger, $error);

        \Phake::verify($logger, \Phake::times(1))->critical('Message: error');
    }

    private function createApplication(): Application
    {
        $application = new Application($this->container);

        return $application;
    }

    private function givenContainer_get_returnsRequest(): RequestInterface
    {
        $request = \Phake::mock(RequestInterface::class);
        \Phake::when($this->container)->get(self::REQUEST_ID)->thenReturn($request);

        return $request;
    }

    private function givenContainer_get_returnsRouter(): RouterInterface
    {
        $router = \Phake::mock(RouterInterface::class);

        \Phake::when($this->container)->get(self::ROUTER_ID)->thenReturn($router);

        return $router;
    }

    private function givenContainer_get_returnsResponseSender(): ResponseSenderInterface
    {
        $responseSender = \Phake::mock(ResponseSenderInterface::class);

        \Phake::when($this->container)->get(self::RESPONSE_SENDER_ID)->thenReturn($responseSender);

        return $responseSender;
    }

    private function givenContainer_get_returnsResponseFactory(): ResponseFactoryInterface
    {
        $responseFactory = \Phake::mock(ResponseFactoryInterface::class);

        \Phake::when($this->container)->get(self::RESPONSE_FACTORY_ID)->thenReturn($responseFactory);

        return $responseFactory;
    }

    private function givenContainer_get_returnsLogger(): LoggerInterface
    {
        $logger = $this->givenLogger();

        \Phake::when($this->container)->has(self::LOGGER_ID)->thenReturn(true);
        \Phake::when($this->container)->get(self::LOGGER_ID)->thenReturn($logger);

        return $logger;
    }

    private function givenRouter_getRoute_returnsRoute(RouterInterface $router, RequestInterface $request): void
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

    private function givenController_runAction_returns($controller): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);

        \Phake::when($controller)->runAction(self::ACTION_ID, self::LOCATION)->thenReturn($response);

        return $response;
    }

    private function givenContainer_get_throwsException(): void
    {
        \Phake::when($this->container)
            ->get(\Phake::anyParameters())
            ->thenThrow(new \Exception(self::EXCEPTION_MESSAGE));
    }

    private function assertResponseSender_send_isCalledOnce(
        ResponseSenderInterface $responseSender,
        ResponseInterface $response
    ): void {
        \Phake::verify($responseSender, \Phake::times(1))->send($response);
    }

    private function givenRouter_getRoute_throwsException(RouterInterface $router): void
    {
        \Phake::when($router)->getRoute(\Phake::anyParameters())->thenThrow(new \Exception());
    }

    private function givenResponseFactory_createExceptionResponse_returnsResponse($responseFactory): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);

        \Phake::when($responseFactory)
            ->createExceptionResponse(\Phake::anyParameters())
            ->thenReturn($response);

        return $response;
    }
}
