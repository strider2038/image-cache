<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\ActionFactoryInterface;
use Strider2038\ImgCache\Core\ActionInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\SecurityInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Service\ImageController;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageControllerTest extends TestCase
{
    private const LOCATION = '/a.jpg';
    private const ACTION_ID = 'test';

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ActionFactoryInterface */
    private $actionFactory;

    /** @var SecurityInterface */
    private $security;

    protected function setUp()
    {
        $this->responseFactory = \Phake::mock(ResponseFactoryInterface::class);
        $this->actionFactory = \Phake::mock(ActionFactoryInterface::class);
        $this->security = \Phake::mock(SecurityInterface::class);
    }

    /**
     * @test
     * @param string $actionId
     * @param int $expectedHttpStatusCode
     * @dataProvider safeActionsProvider
     */
    public function runAction_givenActionAndSecurityReturnsFalse_responseIsReturned(
        string $actionId,
        int $expectedHttpStatusCode
    ): void {
        $controller = $this->createImageController();
        $this->givenSecurity_isAuthorized_returnsFalse();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithForbiddenCode();
        $action = $this->givenActionFactory_createAction_returnsAction();
        $this->givenAction_run_returnsResponseWithStatusCodeOk($action);

        $response = $controller->runAction($actionId, self::LOCATION);

        $this->assertEquals($expectedHttpStatusCode, $response->getStatusCode()->getValue());
    }

    public function safeActionsProvider(): array
    {
        return [
            ['get', HttpStatusCodeEnum::OK],
            ['create', HttpStatusCodeEnum::FORBIDDEN],
            ['replace', HttpStatusCodeEnum::FORBIDDEN],
            ['delete', HttpStatusCodeEnum::FORBIDDEN],
        ];
    }

    private function createImageController(): ImageController
    {
        $controller = new ImageController(
            $this->responseFactory,
            $this->actionFactory,
            $this->security
        );

        return $controller;
    }

    private function givenSecurity_isAuthorized_returnsFalse(): void
    {
        \Phake::when($this->security)->isAuthorized()->thenReturn(false);
    }

    private function givenActionFactory_createAction_returnsAction(): ActionInterface
    {
        $action = \Phake::mock(ActionInterface::class);
        \Phake::when($this->actionFactory)->createAction(\Phake::anyParameters())->thenReturn($action);

        return $action;
    }

    private function givenAction_run_returnsResponseWithStatusCodeOk(ActionInterface $action): void
    {
        $returnedResponse = \Phake::mock(ResponseInterface::class);
        \Phake::when($action)->run()->thenReturn($returnedResponse);
        $statusCode = new HttpStatusCodeEnum(HttpStatusCodeEnum::OK);
        \Phake::when($returnedResponse)->getStatusCode()->thenReturn($statusCode);
    }

    private function givenResponseFactory_createMessageResponse_returnsResponseWithForbiddenCode(): void
    {
        $response = \Phake::mock(ResponseInterface::class);

        \Phake::when($response)
            ->getStatusCode()
            ->thenReturn(new HttpStatusCodeEnum(HttpStatusCodeEnum::FORBIDDEN));

        \Phake::when($this->responseFactory)
            ->createMessageResponse(\Phake::anyParameters())
            ->thenReturn($response);
    }
}
