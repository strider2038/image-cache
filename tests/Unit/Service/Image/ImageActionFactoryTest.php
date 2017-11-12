<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service\Image;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Imaging\DeprecatedImageCacheInterface;
use Strider2038\ImgCache\Service\Image\CreateAction;
use Strider2038\ImgCache\Service\Image\DeleteAction;
use Strider2038\ImgCache\Service\Image\GetAction;
use Strider2038\ImgCache\Service\Image\ImageActionFactory;
use Strider2038\ImgCache\Service\Image\ReplaceAction;

class ImageActionFactoryTest extends TestCase
{
    private const LOCATION = 'location';
    private const ACTION_ID_INVALID = 'invalid';

    /** @var DeprecatedImageCacheInterface */
    private $imageCache;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var RequestInterface */
    private $request;

    protected function setUp(): void
    {
        $this->imageCache = \Phake::mock(DeprecatedImageCacheInterface::class);
        $this->responseFactory = \Phake::mock(ResponseFactoryInterface::class);
        $this->request = \Phake::mock(RequestInterface::class);
    }

    /**
     * @test
     * @dataProvider actionIdAndClassProvider
     * @param string $actionId
     * @param string $actionClass
     */
    public function createAction_givenActionIdAndLocation_actionIsCreated(string $actionId, string $actionClass): void
    {
        $factory = $this->createFactory();

        $action = $factory->createAction($actionId, self::LOCATION);

        $this->assertInstanceOf($actionClass, $action);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Action "invalid" not found
     */
    public function createAction_givenActionIdNotExist_exceptionThrown(): void
    {
        $factory = $this->createFactory();

        $factory->createAction(self::ACTION_ID_INVALID, self::LOCATION);
    }

    public function actionIdAndClassProvider(): array
    {
        return [
            ['get', GetAction::class],
            ['create', CreateAction::class],
            ['replace', ReplaceAction::class],
            ['delete', DeleteAction::class],
        ];
    }

    protected function createFactory(): ImageActionFactory
    {
        return new ImageActionFactory($this->imageCache, $this->responseFactory, $this->request);
    }
}
