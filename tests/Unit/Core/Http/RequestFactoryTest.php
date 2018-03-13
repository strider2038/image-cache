<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core\Http;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\Request;
use Strider2038\ImgCache\Core\Http\RequestFactory;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpProtocolVersionEnum;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;

class RequestFactoryTest extends TestCase
{
    private const PHP_INPUT = 'php://input';
    private const REQUEST_URI_VALUE = 'http://example.org';
    private const AUTHORIZATION_HEADER_VALUE = 'Bearer {token}';
    private const INPUT_STREAM_CONTENTS = 'input_stream_contents';

    /** @var StreamFactoryInterface */
    private $streamFactory;

    protected function setUp(): void
    {
        $this->streamFactory = \Phake::mock(StreamFactoryInterface::class);
    }

    /** @test */
    public function createRequest_givenServerConfiguration_requestIsCreatedAndReturned(): void
    {
        $serverConfiguration = [
            'REQUEST_METHOD' => HttpMethodEnum::GET,
            'REQUEST_URI' => self::REQUEST_URI_VALUE,
            'HTTP_AUTHORIZATION' => self::AUTHORIZATION_HEADER_VALUE,
        ];
        $factory = $this->createRequestFactory();
        $inputStream = $this->givenStreamFactory_createStreamByParameters_returnsStream();
        $this->givenStream_getContents_returnsString($inputStream, self::INPUT_STREAM_CONTENTS);
        $streamCopy = $this->givenStreamFactory_createStreamFromData_returnsStream();

        $request = $factory->createRequest($serverConfiguration);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals(HttpMethodEnum::GET, $request->getMethod());
        $this->assertEquals(self::REQUEST_URI_VALUE, $request->getUri());
        $this->assertStreamFactory_createStreamByParameters_isCalledOnceWithDescriptorAndMode(
            self::PHP_INPUT,
            new ResourceStreamModeEnum(ResourceStreamModeEnum::READ_ONLY)
        );
        $this->assertStream_getContents_isCalledOnce($inputStream);
        $this->assertStreamFactory_createStreamFromData_isCalledOnceWithData(self::INPUT_STREAM_CONTENTS);
        $this->assertSame($streamCopy, $request->getBody());
        $this->assertEquals(HttpProtocolVersionEnum::V1_1, $request->getProtocolVersion()->getValue());
        $this->assertRequestHeadersAreValid($request);
    }

    /**
     * @test
     * @param null|string $givenServerProtocol
     * @param string $expectedServerProtocol
     * @dataProvider serverProtocolProvider
     */
    public function createRequest_givenServerConfigurationWithServerProtocol_requestWithValidServerProtocolIsReturned(
        ?string $givenServerProtocol,
        string $expectedServerProtocol
    ): void {
        $serverConfiguration = [
            'REQUEST_METHOD' => HttpMethodEnum::GET,
            'REQUEST_URI' => self::REQUEST_URI_VALUE,
            'SERVER_PROTOCOL' => $givenServerProtocol
        ];
        $factory = $this->createRequestFactory();
        $this->givenStreamFactory_createStreamByParameters_returnsStream();
        $this->givenStreamFactory_createStreamFromData_returnsStream();

        $request = $factory->createRequest($serverConfiguration);

        $this->assertEquals($expectedServerProtocol, $request->getProtocolVersion()->getValue());
    }

    public function serverProtocolProvider(): array
    {
        return [
            [null, '1.1'],
            ['HTTP/1.0', '1.0'],
            ['HTTP/1.1', '1.1'],
        ];
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Unsupported http method
     */
    public function createRequest_givenInvalidHttpMethod_exceptionThrown(): void
    {
        $serverConfiguration = ['REQUEST_METHOD' => 'Unknown'];
        $factory = $this->createRequestFactory();

        $factory->createRequest($serverConfiguration);
    }

    private function createRequestFactory(): RequestFactory
    {
        return new RequestFactory($this->streamFactory);
    }

    private function assertStreamFactory_createStreamByParameters_isCalledOnceWithDescriptorAndMode(
        string $expectedDescriptor,
        ResourceStreamModeEnum $expectedMode
    ): void {
        \Phake::verify($this->streamFactory, \Phake::times(1))
            ->createStreamByParameters($expectedDescriptor, \Phake::capture($mode));
        /** @var ResourceStreamModeEnum $mode */
        $this->assertEquals($expectedMode->getValue(), $mode->getValue());
    }

    private function givenStreamFactory_createStreamByParameters_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->streamFactory)->createStreamByParameters(\Phake::anyParameters())->thenReturn($stream);

        return $stream;
    }

    private function assertRequestHeadersAreValid(RequestInterface $request): void
    {
        $headers = $request->getHeaders();
        $this->assertCount(1, $headers);
        $authorizationHeader = $headers->get('Authorization');
        $this->assertCount(1, $authorizationHeader);
        $this->assertEquals(self::AUTHORIZATION_HEADER_VALUE, $authorizationHeader->first());
    }

    private function assertStream_getContents_isCalledOnce(StreamInterface $inputStream): void
    {
        \Phake::verify($inputStream, \Phake::times(1))
            ->getContents();
    }

    private function givenStream_getContents_returnsString(StreamInterface $inputStream, string $contents): void
    {
        \Phake::when($inputStream)
            ->getContents()
            ->thenReturn($contents);
    }

    private function assertStreamFactory_createStreamFromData_isCalledOnceWithData(string $data): void
    {
        \Phake::verify($this->streamFactory, \Phake::times(1))
            ->createStreamFromData($data);
    }

    private function givenStreamFactory_createStreamFromData_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->streamFactory)
            ->createStreamFromData(\Phake::anyParameters())
            ->thenReturn($stream);

        return $stream;
    }
}
