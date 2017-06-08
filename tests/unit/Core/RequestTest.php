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
     * @expectedException Strider2038\ImgCache\Exception\RequestException
     * @expectedExceptionMessage Unknown request method
     */
    public function testConstructionFailesWithoutServerRequestMethod()
    {
        new Request();
    }

    public function testConstructionAvailableMethods()
    {
        $methods = [
            'GET', 'POST', 'PUT', 'PATCH', 'DELETE'
        ];
        
        foreach ($methods as $method) {
            $_SERVER['REQUEST_METHOD'] = $method;
            $request = new Request();
            $this->assertEquals($method, $request->getMethod());
        }
    }
    
    public function testGetHeaders()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer xxx';
        
        $request = new Request();
        $this->assertEquals(
            'Bearer xxx', 
            $request->getHeader(Request::HEADER_AUTHORIZATION)
        );
        $this->assertNull($request->getHeader('UNKNOWN_HEADER'));
    }
}
