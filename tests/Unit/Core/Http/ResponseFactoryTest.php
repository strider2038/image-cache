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

use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\Response;
use Strider2038\ImgCache\Core\Http\ResponseFactory;
use Strider2038\ImgCache\Core\ReadOnlyResourceStream;
use Strider2038\ImgCache\Enum\HttpHeader;
use Strider2038\ImgCache\Enum\HttpProtocolVersion;
use Strider2038\ImgCache\Enum\HttpStatusCode;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ResponseFactoryTest extends FileTestCase
{
    private const MESSAGE = 'message';
    private const CONTENT_TYPE_IMAGE_JPEG = 'image/jpeg';

    /** @var RequestInterface */
    private $request;

    protected function setUp()
    {
        parent::setUp();
        $this->request = \Phake::mock(RequestInterface::class);
    }

    /** @test */
    public function createMessageResponse_givenHttpStatusCodeAndMessage_responseIsCreated(): void
    {
        $factory = $this->createResponseFactory();
        $statusCode = new HttpStatusCode(HttpStatusCode::OK);
        $this->givenRequest_getProtocolVersion_returns();

        $response = $factory->createMessageResponse($statusCode, self::MESSAGE);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(HttpStatusCode::OK, $response->getStatusCode()->getValue());
        $this->assertEquals(self::MESSAGE, $response->getBody()->getContents());
        $this->assertEquals(HttpProtocolVersion::V1_0, $response->getProtocolVersion()->getValue());
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

        $response = $factory->createExceptionResponse($exception);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($expectedHttpStatusCode, $response->getStatusCode()->getValue());
        $this->assertEquals(self::MESSAGE, $response->getBody()->getContents());
    }

    public function exceptionAndHttpCodesProvider(): array
    {
        return [
            [0, HttpStatusCode::INTERNAL_SERVER_ERROR],
            [500, HttpStatusCode::INTERNAL_SERVER_ERROR],
            [400, HttpStatusCode::BAD_REQUEST],
        ];
    }

    /** @test */
    public function createExceptionResponse_givenExceptionAndIsDebugged_responseIsCreated(): void {
        $factory = $this->createResponseFactory(true);
        $exception = new \Exception(self::MESSAGE, HttpStatusCode::INTERNAL_SERVER_ERROR);

        $response = $factory->createExceptionResponse($exception);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(HttpStatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode()->getValue());
        $this->assertRegExp('/Application exception #500 .*/', $response->getBody()->getContents());
    }

    /** @test */
    public function createFileResponse_givenImage_responseWithContentHeaderIsCreated(): void
    {
        $factory = $this->createResponseFactory();
        $statusCode = new HttpStatusCode(HttpStatusCode::CREATED);
        $filename = $this->givenAssetFile(self::IMAGE_BOX_JPG);

        $response = $factory->createFileResponse($statusCode, $filename);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(HttpStatusCode::CREATED, $response->getStatusCode()->getValue());
        $this->assertInstanceOf(ReadOnlyResourceStream::class, $response->getBody());
        $this->assertEquals(file_get_contents($filename), $response->getBody()->getContents());
        $this->assertEquals(
            self::CONTENT_TYPE_IMAGE_JPEG,
            $response->getHeaderLine(new HttpHeader(HttpHeader::CONTENT_TYPE))
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
        $statusCode = new HttpStatusCode(HttpStatusCode::OK);

        $factory->createFileResponse($statusCode, self::FILENAME_NOT_EXIST);
    }

    private function createResponseFactory(bool $isDebugged = false): ResponseFactory
    {
        $factory = new ResponseFactory($this->request, $isDebugged);

        return $factory;
    }

    private function givenRequest_getProtocolVersion_returns(): void
    {
        \Phake::when($this->request)
            ->getProtocolVersion()
            ->thenReturn(new HttpProtocolVersion(HttpProtocolVersion::V1_0));
    }
}
