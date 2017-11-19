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
use Strider2038\ImgCache\Utility\RequestLoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationTest extends TestCase 
{
    use ProviderTrait, LoggerTrait;

    private const REQUEST_ID = 'request';
    private const REQUEST_LOGGER_ID = 'request_logger';
    private const RESPONSE_FACTORY_ID = 'response_factory';
    private const RESPONSE_SENDER_ID = 'response_sender';
    private const ROUTER_ID = 'router';
    private const LOGGER_ID = 'logger';
    private const CONTROLLER_ID = 'controller';
    private const ACTION_ID = 'action';
    private const EXCEPTION_MESSAGE = 'application exception message';

    /** @var ContainerInterface */
    private $container;

    /** @var RequestInterface */
    private $request;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ResponseSenderInterface */
    private $responseSender;

    /** @var RouterInterface */
    private $router;

    /** @var LoggerInterface */
    private $logger;

    /** @var RequestLoggerInterface */
    private $requestLogger;

    protected function setUp(): void
    {
        $this->container = \Phake::mock(ContainerInterface::class);
        $this->request = \Phake::mock(RequestInterface::class);
        $this->responseFactory = \Phake::mock(ResponseFactoryInterface::class);
        $this->responseSender = \Phake::mock(ResponseSenderInterface::class);
        $this->router = \Phake::mock(RouterInterface::class);
        $this->logger = \Phake::mock(LoggerInterface::class);
        $this->requestLogger = \Phake::mock(RequestLoggerInterface::class);
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
        $application = $this->createApplicationWithAllServices();
        $routerRequest = $this->givenRequest();
        $this->givenRouter_getRoute_returnsRouteWithParameters(
            self::CONTROLLER_ID,
            self::ACTION_ID,
            $routerRequest
        );
        $controller = $this->givenContainer_get_returnsController(self::CONTROLLER_ID);
        $response = $this->givenController_runAction_returnsResponse($controller);

        $exitCode = $application->run();

        $this->assertEquals(0, $exitCode);
        $this->assertLoggerRequest_logCurrentRequest_isCalledOnce();
        $this->assertRouter_getRoute_isCalledOnceWithRequest($this->request);
        $this->assertContainer_get_isCalledOnceWithControllerId(self::CONTROLLER_ID);
        $this->assertController_runAction_isCalledOnceWithActionIdAndRequest($controller, self::ACTION_ID, $routerRequest);
        $this->assertResponseSender_send_isCalledOnceWithResponse($response);
        $this->assertLogger_debug_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function run_allServicesExistsAndRouterThrowsException_errorResponseIsSent(): void
    {
        $application = $this->createApplicationWithAllServices();
        $this->givenRouter_getRoute_throwsException();
        $response = $this->givenResponseFactory_createExceptionResponse_returnsResponse();

        $exitCode = $application->run();

        $this->assertEquals(1, $exitCode);
        $this->assertLoggerRequest_logCurrentRequest_isCalledOnce();
        $this->assertRouter_getRoute_isCalledOnceWithRequest($this->request);
        $this->assertLogger_error_isCalledOnce($this->logger);
        $this->assertResponseFactory_createExceptionResponse_isCalledOnceWithAnyParameters();
        $this->assertResponseSender_send_isCalledOnceWithResponse($response);
        $this->assertLogger_debug_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function onShutdown_givenLoggerAndError_loggerCriticalIsCalled(): void
    {
        $logger = $this->givenLogger();

        Application::onShutdown($logger, ['message' => 'error']);

        $this->assertLogger_critical_isCalledOnce($logger, 'Message: error');
    }

    private function createApplication(): Application
    {
        return new Application($this->container);
    }

    private function createApplicationWithAllServices(): Application
    {
        \Phake::when($this->container)->get(self::REQUEST_ID)->thenReturn($this->request);
        \Phake::when($this->container)->get(self::RESPONSE_FACTORY_ID)->thenReturn($this->responseFactory);
        \Phake::when($this->container)->get(self::RESPONSE_SENDER_ID)->thenReturn($this->responseSender);
        \Phake::when($this->container)->get(self::ROUTER_ID)->thenReturn($this->router);
        \Phake::when($this->container)->has(self::LOGGER_ID)->thenReturn(true);
        \Phake::when($this->container)->get(self::LOGGER_ID)->thenReturn($this->logger);
        \Phake::when($this->container)->has(self::REQUEST_LOGGER_ID)->thenReturn(true);
        \Phake::when($this->container)->get(self::REQUEST_LOGGER_ID)->thenReturn($this->requestLogger);

        return new Application($this->container);
    }

    private function givenRouter_getRoute_returnsRouteWithParameters(
        string $controllerId,
        string $actionId,
        RequestInterface $request
    ): void {
        $route = \Phake::mock(Route::class);

        \Phake::when($route)->getControllerId()->thenReturn($controllerId);
        \Phake::when($route)->getActionId()->thenReturn($actionId);
        \Phake::when($route)->getRequest()->thenReturn($request);

        \Phake::when($this->router)->getRoute(\Phake::anyParameters())->thenReturn($route);
    }

    private function givenContainer_get_throwsException(): void
    {
        \Phake::when($this->container)
            ->get(\Phake::anyParameters())
            ->thenThrow(new \Exception(self::EXCEPTION_MESSAGE));
    }

    private function assertResponseSender_send_isCalledOnceWithResponse(ResponseInterface $response): void
    {
        \Phake::verify($this->responseSender, \Phake::times(1))->send($response);
    }

    private function givenRouter_getRoute_throwsException(): void
    {
        \Phake::when($this->router)->getRoute(\Phake::anyParameters())->thenThrow(new \Exception());
    }

    private function givenResponseFactory_createExceptionResponse_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);

        \Phake::when($this->responseFactory)
            ->createExceptionResponse(\Phake::anyParameters())
            ->thenReturn($response);

        return $response;
    }

    private function givenRequest(): RequestInterface
    {
        return \Phake::mock(RequestInterface::class);
    }

    private function givenContainer_get_returnsController(string $controllerId): ControllerInterface
    {
        $controller = \Phake::mock(ControllerInterface::class);
        \Phake::when($this->container)->get($controllerId)->thenReturn($controller);

        return $controller;
    }

    private function givenController_runAction_returnsResponse(ControllerInterface $controller): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($controller)->runAction(\Phake::anyParameters())->thenReturn($response);

        return $response;
    }

    private function assertContainer_get_isCalledOnceWithControllerId(string $controllerId): void
    {
        \Phake::verify($this->container, \Phake::times(1))->get($controllerId);
    }

    private function assertController_runAction_isCalledOnceWithActionIdAndRequest(
        ControllerInterface $controller,
        string $actionId,
        RequestInterface $routerRequest
    ): void {
        \Phake::verify($controller, \Phake::times(1))->runAction($actionId, $routerRequest);
    }

    private function assertRouter_getRoute_isCalledOnceWithRequest(RequestInterface $request): void
    {
        \Phake::verify($this->router, \Phake::times(1))->getRoute($request);
    }

    private function assertResponseFactory_createExceptionResponse_isCalledOnceWithAnyParameters(): void
    {
        \Phake::verify($this->responseFactory, \Phake::times(1))->createExceptionResponse(\Phake::anyParameters());
    }

    private function assertLoggerRequest_logCurrentRequest_isCalledOnce(): void
    {
        \Phake::verify($this->requestLogger, \Phake::times(1))->logCurrentRequest();
    }
}
