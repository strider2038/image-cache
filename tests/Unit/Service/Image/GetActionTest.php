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
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Service\Image\GetAction;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class GetActionTest extends TestCase
{
    use ResponseFactoryTrait;

    private const LOCATION = 'a.jpg';
    private const IMAGE_FILENAME = 'image.jpg';

    /** @var ImageCacheInterface */
    private $imageCache;

    protected function setUp()
    {
        $this->imageCache = \Phake::mock(ImageCacheInterface::class);
        $this->givenResponseFactory();
    }

    /** @test */
    public function run_fileDoesNotExistInCache_notFoundResponseIsReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageCache_get_returns(null);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::NOT_FOUND);

        $response = $action->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::NOT_FOUND);
    }

    /** @test */
    public function run_fileExistsInCache_fileResponseIsReturned(): void
    {
        $controller = $this->createAction();
        $image = $this->givenImage();
        $this->givenImageCache_get_returns($image);
        $this->givenResponseFactory_createFileResponse_returnsResponse();

        $response = $controller->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createFileResponse_isCalledOnceWith(
            HttpStatusCodeEnum::CREATED,
            self::IMAGE_FILENAME
        );
    }

    private function createAction(): GetAction
    {
        return new GetAction(self::LOCATION, $this->imageCache, $this->responseFactory);
    }

    private function givenImage(): ImageFile
    {
        $image = \Phake::mock(ImageFile::class);
        \Phake::when($image)->getFilename()->thenReturn(self::IMAGE_FILENAME);

        return $image;
    }

    private function givenImageCache_get_returns(?ImageFile $image): void
    {
        \Phake::when($this->imageCache)->get(self::LOCATION)->thenReturn($image);
    }
}
