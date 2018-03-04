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
use Strider2038\ImgCache\Core\NotAllowedRequestHandler;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Service\Image\CreateImageHandler;
use Strider2038\ImgCache\Service\Image\DeleteImageHandler;
use Strider2038\ImgCache\Service\Image\GetImageHandlerAction;
use Strider2038\ImgCache\Service\Image\ImageRequestHandlerFactory;
use Strider2038\ImgCache\Service\Image\ReplaceImageHandler;

class ImageRequestHandlerFactoryTest extends TestCase
{
    /** @var GetImageHandlerAction */
    private $getAction;
    /** @var CreateImageHandler */
    private $createAction;
    /** @var ReplaceImageHandler */
    private $replaceAction;
    /** @var DeleteImageHandler */
    private $deleteAction;

    protected function setUp(): void
    {
        $this->getAction = \Phake::mock(GetImageHandlerAction::class);
        $this->createAction = \Phake::mock(CreateImageHandler::class);
        $this->replaceAction = \Phake::mock(ReplaceImageHandler::class);
        $this->deleteAction = \Phake::mock(DeleteImageHandler::class);
    }

    /**
     * @test
     * @dataProvider httpMethodAndHandlerClassProvider
     * @param string $httpMethod
     * @param string $requestHandlerClass
     */
    public function createRequestHandlerByHttpMethod_givenActionId_actionReturned(string $httpMethod, string $requestHandlerClass): void
    {
        $factory = $this->createFactory();
        $method = new HttpMethodEnum($httpMethod);

        $handler = $factory->createRequestHandlerByParameters($method);

        $this->assertInstanceOf($requestHandlerClass, $handler);
    }

    /**
     * @test
     * @dataProvider httpMethodForNullAction
     * @param string $httpMethod
     */
    public function createRequestHandlerByHttpMethod_givenActionId_nullActionReturned(string $httpMethod): void
    {
        $factory = new ImageRequestHandlerFactory($this->getAction);
        $method = new HttpMethodEnum($httpMethod);

        $handler = $factory->createRequestHandlerByParameters($method);

        $this->assertInstanceOf(NotAllowedRequestHandler::class, $handler);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Handler for http method "PATCH" not found
     */
    public function createAction_givenActionIdNotExist_exceptionThrown(): void
    {
        $factory = $this->createFactory();
        $method = new HttpMethodEnum(HttpMethodEnum::PATCH);

        $factory->createRequestHandlerByParameters($method);
    }

    public function httpMethodAndHandlerClassProvider(): array
    {
        return [
            [HttpMethodEnum::GET, GetImageHandlerAction::class],
            [HttpMethodEnum::POST, CreateImageHandler::class],
            [HttpMethodEnum::PUT, ReplaceImageHandler::class],
            [HttpMethodEnum::DELETE, DeleteImageHandler::class],
        ];
    }

    public function httpMethodForNullAction(): array
    {
        return [
            [HttpMethodEnum::POST],
            [HttpMethodEnum::PUT],
            [HttpMethodEnum::DELETE],
        ];
    }

    protected function createFactory(): ImageRequestHandlerFactory
    {
        return new ImageRequestHandlerFactory(
            $this->getAction,
            $this->createAction,
            $this->replaceAction,
            $this->deleteAction
        );
    }
}
