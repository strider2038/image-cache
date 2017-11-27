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
use Strider2038\ImgCache\Core\ReadOnlyResourceStream;
use Strider2038\ImgCache\Core\StreamFactoryInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpProtocolVersionEnum;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;

class RequestFactoryTest extends TestCase
{
    private const PHP_INPUT = 'php://input';
    private const REQUEST_URI_VALUE = 'http://example.org';

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
        ];
        $factory = $this->createRequestFactory();
        $stream = $this->givenStreamFactory_createStreamByParameters_returnsStream();

        $request = $factory->createRequest($serverConfiguration);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals(HttpMethodEnum::GET, $request->getMethod());
        $this->assertEquals(self::REQUEST_URI_VALUE, $request->getUri());
        $this->assertStreamFactory_createStreamByParameters_isCalledOnceWithDescriptorAndMode(
            self::PHP_INPUT,
            new ResourceStreamModeEnum(ResourceStreamModeEnum::READ_ONLY)
        );
        $this->assertSame($stream, $request->getBody());
        $this->assertEquals(HttpProtocolVersionEnum::V1_1, $request->getProtocolVersion()->getValue());
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
}
