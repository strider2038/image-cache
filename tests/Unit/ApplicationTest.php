<?php

namespace Strider2038\ImgCache\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\CoreServicesContainer;
use Strider2038\ImgCache\Core\CoreServicesContainerInterface;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
use Strider2038\ImgCache\Core\HttpServicesContainer;
use Strider2038\ImgCache\Core\HttpServicesContainerInterface;
use Strider2038\ImgCache\Core\ServiceLoaderInterface;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationTest extends TestCase 
{
    use LoggerTrait;

    /** @var ContainerInterface */
    private $container;

    /** @var LoggerInterface */
    private $logger;

    /** @var ServiceLoaderInterface */
    private $serviceLoader;

    /** @var RequestInterface */
    private $request;

    /** @var RequestHandlerInterface */
    private $requestHandler;

    /** @var ResponseSenderInterface */
    private $responseSender;

    protected function setUp(): void
    {
        $this->container = \Phake::mock(ContainerInterface::class);
        $this->logger = \Phake::mock(LoggerInterface::class);
        $this->serviceLoader = \Phake::mock(ServiceLoaderInterface::class);
        $this->request = \Phake::mock(RequestInterface::class);
        $this->requestHandler = \Phake::mock(RequestHandlerInterface::class);
        $this->responseSender = \Phake::mock(ResponseSenderInterface::class);
    }

    /** @test */
    public function run_givenContainerWithCoreServices_servicesLoadedAndRequestHandledAndResponseSent(): void
    {
        $application = $this->createApplication();
        $this->givenContainer_get_returnsCoreServices(CoreServicesContainerInterface::class);
        $this->givenContainer_get_returnsHttpServices(HttpServicesContainerInterface::class);
        $response = $this->givenRequestHandler_handleRequest_returnsResponse();

        $application->run();

        $this->assertContainer_get_isCalledOnceWithServiceId(CoreServicesContainerInterface::class);
        $this->assertContainer_get_isCalledOnceWithServiceId(HttpServicesContainerInterface::class);
        $this->assertLogger_debug_isCalledTimes($this->logger, 2);
        $this->assertServiceLoader_loadServices_isCalledOnceWithContainer($this->container);
        $this->assertRequestHandler_handleRequest_isCalledOnceWithRequest($this->request);
        $this->assertResponseSender_send_isCalledOnceWithResponse($response);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @group separate
     */
    public function run_containerIsEmpty_serverErrorReturned(): void
    {
        $this->givenContainer_get_throwsException();
        $application = $this->createApplication();

        $application->run();

        $this->assertEquals(500, http_response_code());
        $this->expectOutputString('Application fatal error.');
    }

    private function assertContainer_get_isCalledOnceWithServiceId(string $id): void
    {
        \Phake::verify($this->container, \Phake::times(1))->get($id);
    }

    private function createApplication(): Application
    {
        return new Application($this->container);
    }

    private function givenContainer_get_returnsCoreServices(string $id): void
    {
        $coreServices = new CoreServicesContainer(
            $this->logger,
            $this->serviceLoader
        );
        \Phake::when($this->container)->get($id)->thenReturn($coreServices);
    }

    private function givenContainer_get_returnsHttpServices(string $id): void
    {
        $coreServices = new HttpServicesContainer(
            $this->request,
            $this->requestHandler,
            $this->responseSender
        );
        \Phake::when($this->container)->get($id)->thenReturn($coreServices);
    }

    private function givenContainer_get_throwsException(): void
    {
        \Phake::when($this->container)
            ->get(\Phake::anyParameters())
            ->thenThrow(new \Exception('exception_message'));
    }

    private function givenRequestHandler_handleRequest_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->requestHandler)->handleRequest(\Phake::anyParameters())->thenReturn($response);

        return $response;
    }

    private function assertServiceLoader_loadServices_isCalledOnceWithContainer(ContainerInterface $container): void
    {
        \Phake::verify($this->serviceLoader, \Phake::times(1))->loadServices($container);
    }

    private function assertRequestHandler_handleRequest_isCalledOnceWithRequest(RequestInterface $request): void
    {
        \Phake::verify($this->requestHandler, \Phake::times(1))->handleRequest($request);
    }

    private function assertResponseSender_send_isCalledOnceWithResponse($response): void
    {
        \Phake::verify($this->responseSender, \Phake::times(1))->send($response);
    }
}
