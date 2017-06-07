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
use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\{
    Security,
    Request
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SecurityTest extends TestCase 
{

    const TEST_TOKEN = '12345678901234567890123456789012';
    
    /** @var \Strider2038\ImgCache\Application */
    private $app;

    public function setUp() 
    {
        $this->app = new class extends Application {
            public function __construct() {
                parent::__construct(['id' => 'test']);
            }
        };
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionMessage Access token is insecure
     */
    public function testSetTokenIsInsecure() 
    {
        $security = new Security($this->app);
        $security->setAccessToken('123');
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionMessage Access token is already set
     */
    public function testSetTokenIsAlreadySet() 
    {
        $security = new Security($this->app);
        $security->setAccessToken(self::TEST_TOKEN);
        $security->setAccessToken(self::TEST_TOKEN);
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionMessage Access token is not set
     */
    public function testIsTokenValidTokenNotSet() 
    {
        $security = new Security($this->app);
        $security->isTokenValid();
    }
    
    public function testIsTokenValidHeaderNotSet() 
    {
        $token = self::TEST_TOKEN;
        
        $app = new Application([
            'id' => 'test',
            'components' => [
                'request' => function($app) use ($token) {
                    return new class($app) extends Request {
                        public function __construct() {}
                        public function getHeader(string $key): ?string 
                        {
                            return null;
                        }
                    };
                }
            ]
        ]);
        
        $security = new Security($app);
        $security->setAccessToken($token);
            
        $this->assertFalse($security->isTokenValid());
    }
    
    public function testIsTokenValidSuccess() 
    {
        $token = self::TEST_TOKEN;
        
        $app = new Application([
            'id' => 'test',
            'components' => [
                'request' => function($app) use ($token) {
                    $request = new class($app) extends Request {
                        public $token;
                        public function __construct() {}
                        public function getHeader(string $key): ?string 
                        {
                            return 'Bearer ' . $this->token;
                        }
                    };
                    $request->token = $token;
                    return $request;
                }
            ]
        ]);
        
        $security = new Security($app);
        $security->setAccessToken($token);
            
        $this->assertTrue($security->isTokenValid());
        $this->assertTrue($security->isAuthorized());
    }
    
    public function testIsTokenValidFail() 
    {
        $app = new Application([
            'id' => 'test',
            'components' => [
                'request' => function($app) {
                    return new class($app) extends Request {
                        public function __construct() {}
                        public function getHeader(string $key): ?string 
                        {
                            return 'Bearer 123';
                        }
                    };
                }
            ]
        ]);
        
        $security = new Security($app);
        $security->setAccessToken(self::TEST_TOKEN);
            
        $this->assertFalse($security->isTokenValid());
        $this->assertFalse($security->isAuthorized());
    }
}
