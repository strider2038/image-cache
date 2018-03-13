<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\ReadOnlyAccessControl;
use Strider2038\ImgCache\Enum\HttpMethodEnum;

class ReadOnlyAccessControlTest extends TestCase
{
    /**
     * @test
     * @param string $httpMethod
     * @param bool $expectedCanHandleRequest
     * @dataProvider httpMethodAndCanHandleRequestProvider
     */
    public function canHandleRequest_givenRequestWithHttpMethod_trueReturnedOnlyForGetHttpMethod(
        string $httpMethod,
        bool $expectedCanHandleRequest
    ): void {
        $accessControl = new ReadOnlyAccessControl();
        $request = \Phake::mock(RequestInterface::class);
        $this->givenRequest_getMethod_returnsMethod($request, $httpMethod);

        $canHandleRequest = $accessControl->canHandleRequest($request);

        $this->assertEquals($expectedCanHandleRequest, $canHandleRequest);
        $this->assertRequest_getMethod_isCalledOnce($request);
    }

    public function httpMethodAndCanHandleRequestProvider(): array
    {
        return [
            [HttpMethodEnum::GET, true],
            [HttpMethodEnum::POST, false],
            [HttpMethodEnum::PUT, false],
            [HttpMethodEnum::PATCH, false],
            [HttpMethodEnum::DELETE, false],
        ];
    }

    private function givenRequest_getMethod_returnsMethod(RequestInterface $request, string $httpMethod): void
    {
        \Phake::when($request)->getMethod()->thenReturn(new HttpMethodEnum($httpMethod));
    }

    private function assertRequest_getMethod_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getMethod();
    }
}
