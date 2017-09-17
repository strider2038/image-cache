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
use Strider2038\ImgCache\Core\DeprecatedRequest;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class DeprecatedRequestTest extends TestCase
{
    const REQUEST_URI = 'http://example.org';
    const REQUEST_URI_SCHEME = 'http';

    /**
     * @dataProvider requestMethodsProvider
     */
    public function testConstruct_RequestMethodIsSet_RequestMethodReturned($method)
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        $request = $this->createRequest();

        $this->assertEquals($method, $request->getMethod());
    }
    
    public function requestMethodsProvider(): array
    {
        return [
            ['GET'], 
            ['POST'], 
            ['PUT'], 
            ['PATCH'], 
            ['DELETE']
        ];
    }
    
    public function testConstruct_RequestMethodNotSet_NullReturned(): void
    {   
        unset($_SERVER['REQUEST_METHOD']);

        $request = $this->createRequest();

        $this->assertNull($request->getMethod());
    }
    
    /**
     * @dataProvider headersProvider
     */
    public function testGetHeader_HeaderIsSet_HeaderReturned($header, $value): void
    {
        $_SERVER[$header] = $value;
        $request = $this->createRequest();

        $actualHeader = $request->getHeader(DeprecatedRequest::HEADER_AUTHORIZATION);

        $this->assertEquals($value, $actualHeader);
    }
    
    public function headersProvider(): array
    {
        return [
            ['HTTP_AUTHORIZATION', 'Bearer xxx']
        ];
    }
    
    public function testGetHeader_HeaderIsNotSet_NullReturned(): void
    {
        $request = $this->createRequest();

        $header = $request->getHeader('UNKNOWN_HEADER');

        $this->assertNull($header);
    }

    public function testGetUrl_GivenRequestURIInServer_UrlIsReturned(): void
    {
        $_SERVER['REQUEST_URI'] = self::REQUEST_URI;
        $request = $this->createRequest();

        $url = $request->getUrl();

        $this->assertEquals(self::REQUEST_URI, $url);
    }

    public function testGetUrl_GivenRequestURIInServerAndComponent_ComponentOfUrlIsReturned(): void
    {
        $_SERVER['REQUEST_URI'] = self::REQUEST_URI;
        $request = $this->createRequest();

        $url = $request->getUrl(PHP_URL_SCHEME);

        $this->assertEquals(self::REQUEST_URI_SCHEME, $url);
    }

    private function createRequest(): DeprecatedRequest
    {
        $request = new DeprecatedRequest();

        return $request;
    }
}
