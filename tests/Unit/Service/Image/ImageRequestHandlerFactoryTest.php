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
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactoryInterface;
use Strider2038\ImgCache\Service\Image\CreateImageHandler;
use Strider2038\ImgCache\Service\Image\DeleteImageHandler;
use Strider2038\ImgCache\Service\Image\GetImageHandler;
use Strider2038\ImgCache\Service\Image\ImageHandlerParameters;
use Strider2038\ImgCache\Service\Image\ImageRequestHandlerFactory;
use Strider2038\ImgCache\Service\Image\ReplaceImageHandler;

class ImageRequestHandlerFactoryTest extends TestCase
{
    private const CACHE_DIRECTORY = 'cache_directory';

    /** @var ImageStorageFactoryInterface */
    private $imageStorageFactory;

    /** @var ImageCacheFactoryInterface */
    private $imageCacheFactory;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ImageFilenameFactoryInterface */
    private $filenameFactory;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    protected function setUp(): void
    {
        $this->imageStorageFactory = \Phake::mock(ImageStorageFactoryInterface::class);
        $this->imageCacheFactory = \Phake::mock(ImageCacheFactoryInterface::class);
        $this->responseFactory = \Phake::mock(ResponseFactoryInterface::class);
        $this->filenameFactory = \Phake::mock(ImageFilenameFactoryInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
    }

    /**
     * @test
     * @dataProvider httpMethodAndHandlerClassNameProvider
     * @param string $httpMethod
     * @param string $handlerClassName
     */
    public function createRequestHandlerByParameters_givenHttpMethodAndImageSource_imageRequestHandlerCreatedAndReturned(
        string $httpMethod,
        string $handlerClassName
    ): void {
        $factory = $this->createImageRequestHandlerFactory();
        $imageSource = \Phake::mock(AbstractImageSource::class);
        $parameters = $this->givenImageHandlerParameters($httpMethod, $imageSource);
        $this->givenImageStorageFactory_createImageStorageForImageSource_returnsImageStorage();
        $this->givenImageSource_getCacheDirectory_returnsDirectoryName($imageSource);
        $this->givenImageCacheFactory_createImageCacheForWebDirectory_returnsImageCache();

        $handler = $factory->createRequestHandlerByParameters($parameters);

        $this->assertInstanceOf(RequestHandlerInterface::class, $handler);
        $this->assertImageStorageFactory_createImageStorageForImageSource_isCalledOnceWithImageSource($imageSource);
        $this->assertImageSource_getCacheDirectory_isCalledOnce($imageSource);
        $this->assertImageCacheFactory_createImageCacheForWebDirectory_isCalledOnceWithDirectoryName(self::CACHE_DIRECTORY);
        $this->assertInstanceOf($handlerClassName, $handler);
    }

    public function httpMethodAndHandlerClassNameProvider(): array
    {
        return [
            [HttpMethodEnum::GET, GetImageHandler::class],
            [HttpMethodEnum::POST, CreateImageHandler::class],
            [HttpMethodEnum::PUT, ReplaceImageHandler::class],
            [HttpMethodEnum::DELETE, DeleteImageHandler::class],
        ];
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /Handler for http method .* not found/
     */
    public function createRequestHandlerByParameters_givenInvalidHttpMethodAndImageSource_notFoundExceptionThrown(): void
    {
        $factory = $this->createImageRequestHandlerFactory();
        $httpMethod = HttpMethodEnum::PATCH;
        $imageSource = \Phake::mock(AbstractImageSource::class);
        $parameters = $this->givenImageHandlerParameters($httpMethod, $imageSource);

        $factory->createRequestHandlerByParameters($parameters);
    }

    private function createImageRequestHandlerFactory(): ImageRequestHandlerFactory
    {
        return new ImageRequestHandlerFactory(
            $this->imageStorageFactory,
            $this->imageCacheFactory,
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageFactory
        );
    }

    private function assertImageStorageFactory_createImageStorageForImageSource_isCalledOnceWithImageSource(
        AbstractImageSource $imageSource
    ): void {
        \Phake::verify($this->imageStorageFactory, \Phake::times(1))
            ->createImageStorageForImageSource($imageSource);
    }

    private function assertImageSource_getCacheDirectory_isCalledOnce(AbstractImageSource $imageSource): void
    {
        \Phake::verify($imageSource, \Phake::times(1))
            ->getCacheDirectory();
    }

    private function assertImageCacheFactory_createImageCacheForWebDirectory_isCalledOnceWithDirectoryName(
        string $directoryName
    ): void {
        \Phake::verify($this->imageCacheFactory, \Phake::times(1))
            ->createImageCacheForWebDirectory($directoryName);
    }

    private function givenImageSource_getCacheDirectory_returnsDirectoryName(
        AbstractImageSource $imageSource
    ): void {
        \Phake::when($imageSource)
            ->getCacheDirectory()
            ->thenReturn(self::CACHE_DIRECTORY);
    }

    private function givenImageHandlerParameters(string $httpMethod, AbstractImageSource $imageSource): ImageHandlerParameters
    {
        return new ImageHandlerParameters(
            new HttpMethodEnum($httpMethod),
            $imageSource
        );
    }

    private function givenImageCacheFactory_createImageCacheForWebDirectory_returnsImageCache(): void
    {
        $imageCache = \Phake::mock(ImageCacheInterface::class);
        \Phake::when($this->imageCacheFactory)
            ->createImageCacheForWebDirectory(\Phake::anyParameters())
            ->thenReturn($imageCache);
    }

    private function givenImageStorageFactory_createImageStorageForImageSource_returnsImageStorage(): void
    {
        $imageStorage = \Phake::mock(ImageStorageInterface::class);
        \Phake::when($this->imageStorageFactory)
            ->createImageStorageForImageSource(\Phake::anyParameters())
            ->thenReturn($imageStorage);
    }
}
