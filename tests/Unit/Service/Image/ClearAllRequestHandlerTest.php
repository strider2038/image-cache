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
use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceCollection;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\ImageCacheFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Service\Image\ClearAllRequestHandler;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class ClearAllRequestHandlerTest extends TestCase
{
    private const CACHE_DIRECTORY = '/cache_directory';
    use ResponseFactoryTrait;

    /** @var ImageStorageFactoryInterface */
    private $imageStorageFactory;
    /** @var ImageCacheFactoryInterface */
    private $imageCacheFactory;

    /** @var ImageStorageInterface */
    private $imageStorage;
    /** @var ImageCacheInterface */
    private $imageCache;

    protected function setUp(): void
    {
        $this->imageStorageFactory = \Phake::mock(ImageStorageFactoryInterface::class);
        $this->imageCacheFactory = \Phake::mock(ImageCacheFactoryInterface::class);
        $this->givenResponseFactory();
        $this->imageStorage = \Phake::mock(ImageStorageInterface::class);
        $this->imageCache = \Phake::mock(ImageCacheInterface::class);
    }

    /** @test  */
    public function handleRequest_givenRequestAndImageSources_deleteAllImagesFromStoragesAndCache(): void
    {
        $imageSource = \Phake::mock(AbstractImageSource::class);
        $requestHandler = $this->createClearAllRequestHandler($imageSource);
        $request = \Phake::mock(RequestInterface::class);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::OK);
        $this->givenImageStorageFactory_createImageStorageForImageSource_returnsImageStorage();
        $this->givenImageSource_getCacheDirectory_returnsCacheDirectory($imageSource);
        $this->givenImageCacheFactory_createImageCacheForWebDirectory_returnsImageCache();

        $response = $requestHandler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertImageStorageFactory_createImageStorageForImageSource_isCalledOnceWithImageSource($imageSource);
        $this->assertImageStorage_cleanDirectory_isCalledOnceWithRootDirectory();
        $this->assertImageSource_getCacheDirectory_isCalledOnce($imageSource);
        $this->assertImageCacheFactory_createImageCacheForWebDirectory_isCalledOnceWithCacheDirectory();
        $this->assertImageCache_cleanDirectory_isCalledOnceWithRootDirectory();
    }

    /** @test  */
    public function handleRequest_givenRequestAndNoImageSources_okResponseReturned(): void
    {
        $requestHandler = $this->createClearAllRequestHandler();
        $request = \Phake::mock(RequestInterface::class);
        $expectedResponse = $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::OK);

        $response = $requestHandler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::OK);
        $this->assertSame($expectedResponse, $response);
    }

    private function createClearAllRequestHandler(AbstractImageSource $imageSource = null): ClearAllRequestHandler
    {
        $imageSourceCollection = new ImageSourceCollection();

        if ($imageSource !== null) {
            $imageSourceCollection->add($imageSource);
        }

        return new ClearAllRequestHandler(
            $imageSourceCollection,
            $this->imageStorageFactory,
            $this->imageCacheFactory,
            $this->responseFactory
        );
    }

    private function assertImageStorageFactory_createImageStorageForImageSource_isCalledOnceWithImageSource(
        AbstractImageSource $imageSource
    ): void {
        \Phake::verify($this->imageStorageFactory, \Phake::times(1))
            ->createImageStorageForImageSource($imageSource);
    }

    private function givenImageStorageFactory_createImageStorageForImageSource_returnsImageStorage(): void
    {
        \Phake::when($this->imageStorageFactory)
            ->createImageStorageForImageSource(\Phake::anyParameters())
            ->thenReturn($this->imageStorage);
    }

    private function assertImageStorage_cleanDirectory_isCalledOnceWithRootDirectory(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))
            ->cleanDirectory(\Phake::capture($directoryName));
        $this->assertEquals('/', $directoryName);
    }

    private function assertImageSource_getCacheDirectory_isCalledOnce(AbstractImageSource $imageSource): void
    {
        \Phake::verify($imageSource, \Phake::times(1))
            ->getCacheDirectory();
    }

    private function givenImageSource_getCacheDirectory_returnsCacheDirectory(AbstractImageSource $imageSource): void
    {
        \Phake::when($imageSource)
            ->getCacheDirectory()
            ->thenReturn(self::CACHE_DIRECTORY);
    }

    private function assertImageCacheFactory_createImageCacheForWebDirectory_isCalledOnceWithCacheDirectory(): void
    {
        \Phake::verify($this->imageCacheFactory, \Phake::times(1))
            ->createImageCacheForWebDirectory(self::CACHE_DIRECTORY);
    }

    private function givenImageCacheFactory_createImageCacheForWebDirectory_returnsImageCache(): void
    {
        \Phake::when($this->imageCacheFactory)
            ->createImageCacheForWebDirectory(\Phake::anyParameters())
            ->thenReturn($this->imageCache);
    }

    private function assertImageCache_cleanDirectory_isCalledOnceWithRootDirectory(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))
            ->cleanDirectory('/');
    }
}
