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

    const TEST_TOKEN = '12345678901234567890123456789012';
    
    /** @var Strider2038\ImgCache\Core\RequestInterface */
    private $request;

    public function setUp() 
    {
        $this->request = new class implements RequestInterface {
            public $testHeader = null;
            public function getMethod(): ?string {}
            public function getHeader(string $key): ?string 
            {
                return $this->testHeader;
            }
            public function getUrl(int $component = null): string {}
        };
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Access token is insecure
     */
    public function testConstruct_TokenIsShort_ExceptionThrown(): void
    {
        new Security($this->request, '123');
    }
    
    public function testIsTokenValid_HeaderIsNotSet_FalseReturned() 
    {
        $security = new Security($this->request, self::TEST_TOKEN);
            
        $this->assertFalse($security->isTokenValid());
    }
    
    public function testIsTokenValid_CorrectTokenInHeader_TrueReturned() 
    {
        $this->request->testHeader = 'Bearer ' . self::TEST_TOKEN;
        
        $security = new Security($this->request, self::TEST_TOKEN);
        
        $this->assertTrue($security->isTokenValid());
        $this->assertTrue($security->isAuthorized());
    }
    
    public function testIsTokenValid_IncorrectTokenInHeader_FalseReturned() 
    {
        $this->request->testHeader = 'Bearer 123';
        
        $security = new Security($this->request, self::TEST_TOKEN);
            
        $this->assertFalse($security->isTokenValid());
        $this->assertFalse($security->isAuthorized());
    }
}
