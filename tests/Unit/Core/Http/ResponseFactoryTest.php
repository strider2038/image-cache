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

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\Response;
use Strider2038\ImgCache\Core\Http\ResponseFactory;
use Strider2038\ImgCache\Core\StreamFactoryInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;
use Strider2038\ImgCache\Enum\HttpProtocolVersionEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Tests\Support\Phake\FileOperationsTrait;

class ResponseFactoryTest extends FileTestCase
{
    use FileOperationsTrait;

    private const MESSAGE = 'message';
    private const CONTENT_TYPE_IMAGE_JPEG = 'image/jpeg';

    /** @var RequestInterface */
    private $request;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    /** @var FileOperationsInterface */
    private $fileOperations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = \Phake::mock(RequestInterface::class);
        $this->streamFactory = \Phake::mock(StreamFactoryInterface::class);
        $this->fileOperations = $this->givenFileOperations();
    }

    /** @test */
    public function createMessageResponse_givenHttpStatusCodeAndMessage_responseIsCreated(): void
    {
        $factory = $this->createResponseFactory();
        $statusCode = new HttpStatusCodeEnum(HttpStatusCodeEnum::OK);
        $this->givenRequest_getProtocolVersion_returns();
        $stream = $this->givenStreamFactory_createStreamFromData_returnsStream();

        $response = $factory->createMessageResponse($statusCode, self::MESSAGE);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(HttpStatusCodeEnum::OK, $response->getStatusCode()->getValue());
        $this->assertStreamFactory_createStreamFromData_isCalledOnceWithData(self::MESSAGE);
        $this->assertSame($stream, $response->getBody());
        $this->assertEquals(HttpProtocolVersionEnum::V1_0, $response->getProtocolVersion()->getValue());
    }

    /**
     * @test
     * @param int $exceptionCode
     * @param int $expectedHttpStatusCode
     * @dataProvider exceptionAndHttpCodesProvider
     */
    public function createExceptionResponse_givenException_responseIsCreated(
        int $exceptionCode,
        int $expectedHttpStatusCode
    ): void {
        $factory = $this->createResponseFactory();
        $exception = new \Exception(self::MESSAGE, $exceptionCode);
        $stream = $this->givenStreamFactory_createStreamFromData_returnsStream();

        $response = $factory->createExceptionResponse($exception);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($expectedHttpStatusCode, $response->getStatusCode()->getValue());
        $this->assertStreamFactory_createStreamFromData_isCalledOnceWithData(self::MESSAGE);
        $this->assertSame($stream, $response->getBody());
    }

    public function exceptionAndHttpCodesProvider(): array
    {
        return [
            [0, HttpStatusCodeEnum::INTERNAL_SERVER_ERROR],
            [500, HttpStatusCodeEnum::INTERNAL_SERVER_ERROR],
            [400, HttpStatusCodeEnum::BAD_REQUEST],
        ];
    }

    /** @test */
    public function createExceptionResponse_givenExceptionAndIsDebugged_responseIsCreated(): void {
        $factory = $this->createResponseFactory(true);
        $exception = new \Exception(self::MESSAGE, HttpStatusCodeEnum::INTERNAL_SERVER_ERROR);
        $stream = $this->givenStreamFactory_createStreamFromData_returnsStream();

        $response = $factory->createExceptionResponse($exception);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR, $response->getStatusCode()->getValue());
        $this->assertStreamFactory_createStreamFromData_isCalledOnceWithDataRegExp('/Application exception #500 .*/');
        $this->assertSame($stream, $response->getBody());
    }

    /** @test */
    public function createFileResponse_givenImage_responseWithContentHeaderIsCreated(): void
    {
        $factory = $this->createResponseFactory();
        $statusCode = new HttpStatusCodeEnum(HttpStatusCodeEnum::CREATED);
        $filename = $this->givenAssetFile(self::IMAGE_BOX_JPG);
        $this->givenFileOperations_isFile_returns($this->fileOperations, $filename, true);
        $stream = $this->givenFileOperations_openFile_returnsStream($this->fileOperations);

        $response = $factory->createFileResponse($statusCode, $filename);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode()->getValue());
        $this->assertSame($stream, $response->getBody());
        $this->assertFileOperations_openFile_isCalledOnceWithFilenameAndMode(
            $this->fileOperations,
            $filename,
            ResourceStreamModeEnum::READ_ONLY
        );
        $this->assertEquals(
            self::CONTENT_TYPE_IMAGE_JPEG,
            $response->getHeaderLine(new HttpHeaderEnum(HttpHeaderEnum::CONTENT_TYPE))
        );
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found/
     */
    public function createFileResponse_fileNotExist_exceptionThrown(): void
    {
        $factory = $this->createResponseFactory();
        $statusCode = new HttpStatusCodeEnum(HttpStatusCodeEnum::OK);
        $this->givenFileOperations_isFile_returns($this->fileOperations, self::FILENAME_NOT_EXIST, false);

        $factory->createFileResponse($statusCode, self::FILENAME_NOT_EXIST);
    }

    private function createResponseFactory(bool $isDebugged = false): ResponseFactory
    {
        return new ResponseFactory($this->request, $this->streamFactory, $this->fileOperations, $isDebugged);
    }

    private function givenRequest_getProtocolVersion_returns(): void
    {
        \Phake::when($this->request)
            ->getProtocolVersion()
            ->thenReturn(new HttpProtocolVersionEnum(HttpProtocolVersionEnum::V1_0));
    }

    private function assertStreamFactory_createStreamFromData_isCalledOnceWithData(string $data): void
    {
        \Phake::verify($this->streamFactory, \Phake::times(1))->createStreamFromData($data);
    }

    private function assertStreamFactory_createStreamFromData_isCalledOnceWithDataRegExp(string $dataRegExp): void
    {
        \Phake::verify($this->streamFactory, \Phake::times(1))->createStreamFromData(\Phake::capture($data));
        $this->assertRegExp($dataRegExp, $data);
    }

    private function givenStreamFactory_createStreamFromData_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->streamFactory)->createStreamFromData(\Phake::anyParameters())->thenReturn($stream);

        return $stream;
    }
}
