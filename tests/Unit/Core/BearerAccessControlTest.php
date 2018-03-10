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
use Strider2038\ImgCache\Core\BearerAccessControl;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;

class BearerAccessControlTest extends TestCase
{
    private const EXPECTED_TOKEN = 'expected_token';

    /** @var RequestInterface */
    private $request;

    /**
     * @test
     * @dataProvider AuthorizationHeaderAndCanHandleProvider
     */
    public function canHandleRequest_givenRequestAndToken_boolReturned(
        string $authorizationHeaderValue,
        bool $expectedCanHandleRequest
    ): void {
        $accessControl = new BearerAccessControl(self::EXPECTED_TOKEN);
        $this->givenRequest();
        $this->givenRequest_getHeaderLine_returnsString($authorizationHeaderValue);

        $canHandleRequest = $accessControl->canHandleRequest($this->request);

        $this->assertEquals($expectedCanHandleRequest, $canHandleRequest);
        $this->assertRequest_getHeaderLine_isCalledOnceWithAuthorizationHeader();
    }

    public function AuthorizationHeaderAndCanHandleProvider(): array
    {
        return [
            ['Bearer ' . self::EXPECTED_TOKEN, true],
            ['Bearer invalid_token', false],
        ];
    }

    private function givenRequest(): void
    {
        $this->request = \Phake::mock(RequestInterface::class);
    }

    private function assertRequest_getHeaderLine_isCalledOnceWithAuthorizationHeader(): void
    {
        /** @var HttpHeaderEnum $headerEnum */
        \Phake::verify($this->request, \Phake::times(1))
            ->getHeaderLine(\Phake::capture($headerEnum));
        $this->assertEquals(HttpHeaderEnum::AUTHORIZATION, $headerEnum->getValue());
    }

    private function givenRequest_getHeaderLine_returnsString(string $authorizationHeaderValue): void
    {
        \Phake::when($this->request)
            ->getHeaderLine(\Phake::anyParameters())
            ->thenReturn($authorizationHeaderValue);
    }
}
