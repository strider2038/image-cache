<?php

use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\{
    Component,
    RequestInterface,
    SecurityInterface,
    TemporaryFilesManagerInterface
};
use PHPUnit\Framework\TestCase;

/**
 * Description of ApplicationTest
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationTest extends TestCase 
{
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     */
    public function testConstruct_IdIsNotSet_ExceptionThrown(): void
    {
        $app = new Application([]);
    }
    
    public function testConstruct_IdIsSet_ReturningAppId(): void 
    {
        $app = new Application(['id' => 'test']);
        $this->assertEquals('test', $app->getId());
    }
    
    public function testConstruct_ComponentsInjected_ComponentsAvailable(): void
    {
        $app = new Application([
            'id' => 'test',
            'components' => [
                'request' => function($app) {
                    return new class($app) extends Component implements RequestInterface {
                        public function getMethod(): string 
                        {
                            return 'requestGetMethodResult';
                        }
                        public function getHeader(string $key): ?string 
                        {
                            return 'requestGetHeaderResult';
                        }
                        public function getUrl(int $component = null): string
                        {
                            return 'requestUrl';
                        }
                    };
                },
                'security' => function($app) {
                    return new class($app) extends Component implements SecurityInterface {
                        public function isAuthorized(): bool 
                        {
                            return true;
                        }
                    };
                }
            ],
        ]);
        $this->assertInstanceOf(RequestInterface::class, $app->request);
        $this->assertInstanceOf(SecurityInterface::class, $app->security);
        $this->assertInstanceOf(TemporaryFilesManagerInterface::class, $app->temporaryFileManager);
        $this->assertEquals('requestGetMethodResult', $app->request->getMethod());
        $this->assertEquals('requestGetHeaderResult', $app->request->getHeader(''));
        $this->assertEquals('requestUrl', $app->request->getUrl());
        $this->assertTrue($app->security->isAuthorized());
    }
    
    public function testConstruct_NoComponentsAreSet_CoreComponentsAreAvailable()
    {
        $app = new Application([
            'id' => 'test',
            'params' => [
                'securityToken' => '12345678901234567890123456789012',
            ],
        ]);
        $this->assertInstanceOf(RequestInterface::class, $app->request);
        $this->assertInstanceOf(SecurityInterface::class, $app->security);
        $this->assertInstanceOf(TemporaryFilesManagerInterface::class, $app->temporaryFileManager);
    }
    
    public function testConstruct_ParamIsSet_ParamValueReturned(): void
    {
        $app = new Application([
            'id' => 'test',
            'params' => [
                'param' => 'value',
            ],
        ]);
        $this->assertEquals('value', $app->getParam('param'));
    }
    
    /**
     * @expectedException Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage is not set
     */
    public function testGetParam_ParamIsNotSet_ExceptionThrown(): void
    {
        $app = new Application([
            'id' => 'test',
            'params' => [
                'a' => 'b',
            ],
        ]);
        $this->assertEquals('b', $app->getParam('a'));
        $app->getParam('b');
    }
}
