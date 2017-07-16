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
use Strider2038\ImgCache\Core\{
    Security,
    Request,
    RequestInterface
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SecurityTest extends TestCase 
{
    const VALID_TOKEN = '12345678901234567890123456789012';
    const INVALID_TOKEN = '123';
    const HEADER_AUTH = 'HTTP_AUTHORIZATION';
    
    /** @var RequestInterface */
    private $request;

    public function setUp() 
    {
        $this->request = \Phake::mock(RequestInterface::class);
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Access token is insecure
     */
    public function testConstruct_TokenIsShort_ExceptionThrown(): void
    {
        new Security($this->request, self::INVALID_TOKEN);
    }
    
    public function testIsTokenValid_HeaderIsNotSet_FalseReturned() 
    {
        $security = new Security($this->request, self::VALID_TOKEN);
            
        $this->assertFalse($security->isTokenValid());
    }
    
    public function testIsTokenValid_ValidTokenInHeader_TrueReturned()
    {
        \Phake::when($this->request)
            ->getHeader(self::HEADER_AUTH)
            ->thenReturn('Bearer ' . self::VALID_TOKEN);
        
        $security = new Security($this->request, self::VALID_TOKEN);
        
        $this->assertTrue($security->isTokenValid());
        $this->assertTrue($security->isAuthorized());
    }
    
    public function testIsTokenValid_InvalidTokenInHeader_FalseReturned()
    {
        \Phake::when($this->request)
            ->getHeader(self::HEADER_AUTH)
            ->thenReturn('Bearer 123');
        
        $security = new Security($this->request, self::VALID_TOKEN);
            
        $this->assertFalse($security->isTokenValid());
        $this->assertFalse($security->isAuthorized());
    }
}
