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
use Strider2038\ImgCache\Core\Security;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SecurityTest extends TestCase 
{
    private const VALID_TOKEN = '12345678901234567890123456789012';
    private const INVALID_TOKEN = '123';

    /** @var RequestInterface */
    private $request;

    public function setUp(): void
    {
        $this->request = \Phake::mock(RequestInterface::class);
    }
    
    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Access token is insecure
     */
    public function construct_tokenIsShort_exceptionThrown(): void
    {
        new Security($this->request, self::INVALID_TOKEN);
    }

    /** @test */
    public function isTokenValid_headerIsNotSet_falseReturned()
    {
        $security = new Security($this->request, self::VALID_TOKEN);
            
        $this->assertFalse($security->isTokenValid());
    }

    /** @test */
    public function isTokenValid_validTokenInHeader_trueReturned()
    {
        $this->givenRequest_getHeaderLine_Returns('Bearer ' . self::VALID_TOKEN);

        $security = new Security($this->request, self::VALID_TOKEN);
        
        $this->assertTrue($security->isTokenValid());
        $this->assertTrue($security->isAuthorized());
    }

    /** @test */
    public function isTokenValid_invalidTokenInHeader_falseReturned()
    {
        $this->givenRequest_getHeaderLine_Returns('Bearer 123');
        
        $security = new Security($this->request, self::VALID_TOKEN);
            
        $this->assertFalse($security->isTokenValid());
        $this->assertFalse($security->isAuthorized());
    }

    private function givenRequest_getHeaderLine_Returns(string $value): void
    {
        \Phake::when($this->request)
            ->getHeaderLine(new HttpHeaderEnum(HttpHeaderEnum::AUTHORIZATION))
            ->thenReturn($value);
    }
}
