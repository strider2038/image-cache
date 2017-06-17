<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Request;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RequestTest extends TestCase
{

    /**
     * @dataProvider requestMethodsProvider
     */
    public function testConstruct_RequestMethodIsSet_RequestMethodReturned($method)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $request = new Request();
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
    
    public function testConstruct_RequestMethodNotSet_NullReturned()
    {   
        unset($_SERVER['REQUEST_METHOD']);
        $request = new Request();
        $this->assertNull($request->getMethod());
    }
    
    /**
     * @dataProvider headersProvider
     */
    public function testGetHeader_HeaderIsSet_HeaderReturned($header, $value)
    {
        $_SERVER[$header] = $value;
        
        $request = new Request();
        $this->assertEquals(
            $value, 
            $request->getHeader(Request::HEADER_AUTHORIZATION)
        );
    }
    
    public function headersProvider()
    {
        return [
            ['HTTP_AUTHORIZATION', 'Bearer xxx']
        ];
    }
    
    public function testGetHeader_HeaderIsNotSet_NullReturned()
    {
        $request = new Request();
        $this->assertNull($request->getHeader('UNKNOWN_HEADER'));
    }
}
