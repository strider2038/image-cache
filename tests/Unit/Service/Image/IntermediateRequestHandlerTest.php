<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service\Image;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Service\Image\ImageHandlerParameters;
use Strider2038\ImgCache\Service\Image\ImageRequestHandlerFactoryInterface;
use Strider2038\ImgCache\Service\Image\ImageRequestTransformerInterface;
use Strider2038\ImgCache\Service\Image\ImageSourceDetectorInterface;
use Strider2038\ImgCache\Service\Image\IntermediateRequestHandler;

class IntermediateRequestHandlerTest extends TestCase
{
    /** @var ImageSourceDetectorInterface */
    private $imageSourceDetector;

    /** @var ImageRequestHandlerFactoryInterface */
    private $requestHandlerFactory;

    /** @var ImageRequestTransformerInterface */
    private $requestTransformer;

    protected function setUp(): void
    {
        $this->imageSourceDetector = \Phake::mock(ImageSourceDetectorInterface::class);
        $this->requestHandlerFactory = \Phake::mock(ImageRequestHandlerFactoryInterface::class);
        $this->requestTransformer = \Phake::mock(ImageRequestTransformerInterface::class);
    }

    /** @test */
    public function handleRequest_givenRequest_imageRequestHandlerCreatedAndHandledResponseReturned(): void
    {
        $requestHandler = $this->createIntermediateRequestHandler();
        $request = \Phake::mock(RequestInterface::class);
        $imageSource = $this->givenImageSourceDetector_detectImageSourceByRequest_returnsImageSource();
        $method = $this->givenRequest_getMethod_returnsMethod($request);
        $concreteRequestHandler = $this->givenRequestHandlerFactory_createRequestHandlerByParameters_returnsRequestHandler();
        $transformedRequest = $this->givenRequestTransformer_transformRequestForImageSource_returnsRequest();
        $expectedResponse = $this->givenRequestHandler_handleRequest_returnsResponse($concreteRequestHandler);

        $response = $requestHandler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertImageSourceDetector_detectImageSourceByRequest_isCalledOnceWithRequest($request);
        $this->assertRequest_getMethod_isCalledOnce($request);
        $this->assertRequestHandlerFactory_createRequestHandlerByParameters_isCalledOnceWithMethodAndImageSourceInParameters($method, $imageSource);
        $this->assertRequestTransformer_transformRequestForImageSource_isCalledOnceWithRequestAndImageSource($request, $imageSource);
        $this->assertRequestHandler_handleRequest_isCalledOnceWithRequest($concreteRequestHandler, $transformedRequest);
        $this->assertSame($expectedResponse, $response);
    }

    private function createIntermediateRequestHandler(): IntermediateRequestHandler
    {
        return new IntermediateRequestHandler(
            $this->imageSourceDetector,
            $this->requestHandlerFactory,
            $this->requestTransformer
        );
    }

    private function assertImageSourceDetector_detectImageSourceByRequest_isCalledOnceWithRequest(
        RequestInterface $request
    ): void {
        \Phake::verify($this->imageSourceDetector, \Phake::times(1))
            ->detectImageSourceByRequest($request);
    }

    private function givenImageSourceDetector_detectImageSourceByRequest_returnsImageSource(): AbstractImageSource
    {
        $imageSource = \Phake::mock(AbstractImageSource::class);
        \Phake::when($this->imageSourceDetector)
            ->detectImageSourceByRequest(\Phake::anyParameters())
            ->thenReturn($imageSource);

        return $imageSource;
    }

    private function givenRequest_getMethod_returnsMethod(RequestInterface $request): HttpMethodEnum
    {
        $method = \Phake::mock(HttpMethodEnum::class);
        \Phake::when($request)->getMethod()->thenReturn($method);

        return $method;
    }

    private function assertRequest_getMethod_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getMethod();
    }

    private function givenRequestHandlerFactory_createRequestHandlerByParameters_returnsRequestHandler(): RequestHandlerInterface
    {
        $requestHandler = \Phake::mock(RequestHandlerInterface::class);
        \Phake::when($this->requestHandlerFactory)
            ->createRequestHandlerByParameters(\Phake::anyParameters())
            ->thenReturn($requestHandler);

        return $requestHandler;
    }

    private function assertRequestHandlerFactory_createRequestHandlerByParameters_isCalledOnceWithMethodAndImageSourceInParameters(
        HttpMethodEnum $method,
        AbstractImageSource $imageSource
    ): void {
        /** @var ImageHandlerParameters $parameters */
        \Phake::verify($this->requestHandlerFactory, \Phake::times(1))
            ->createRequestHandlerByParameters(\Phake::capture($parameters));
        $this->assertSame($method, $parameters->getHttpMethod());
        $this->assertSame($imageSource, $parameters->getImageSource());
    }

    private function givenRequestHandler_handleRequest_returnsResponse(
        RequestHandlerInterface $requestHandler
    ): ResponseInterface {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($requestHandler)
            ->handleRequest(\Phake::anyParameters())
            ->thenReturn($response);

        return $response;
    }

    private function assertRequestHandler_handleRequest_isCalledOnceWithRequest(
        RequestHandlerInterface $requestHandler,
        RequestInterface $request
    ): void {
        \Phake::verify($requestHandler, \Phake::times(1))->handleRequest($request);
    }

    private function assertRequestTransformer_transformRequestForImageSource_isCalledOnceWithRequestAndImageSource(
        RequestInterface $request,
        AbstractImageSource $imageSource
    ): void {
        \Phake::verify($this->requestTransformer, \Phake::times(1))
            ->transformRequestForImageSource($request, $imageSource);
    }

    private function givenRequestTransformer_transformRequestForImageSource_returnsRequest(): RequestInterface
    {
        $request = \Phake::mock(RequestInterface::class);
        \Phake::when($this->requestTransformer)
            ->transformRequestForImageSource(\Phake::anyParameters())
            ->thenReturn($request);

        return $request;
    }
}
