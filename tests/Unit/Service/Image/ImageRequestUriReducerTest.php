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
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\UriInterface;
use Strider2038\ImgCache\Imaging\Naming\DirectoryName;
use Strider2038\ImgCache\Service\Image\ImageRequestUriReducer;

class ImageRequestUriReducerTest extends TestCase
{
    private const CACHE_DIRECTORY = '/cache_directory';
    private const REDUCED_URL = '/url';
    private const REQUEST_URL = self::CACHE_DIRECTORY . self::REDUCED_URL;

    /** @var RequestInterface */
    private $request;

    /** @test */
    public function transformRequestForImageSource_givenRequestAndImageSource_uriReducedByCacheDirectoryNameAndRequestReturned(): void
    {
        $reducer = new ImageRequestUriReducer();
        $this->givenRequest();
        $imageSource = \Phake::mock(AbstractImageSource::class);
        $uri = $this->givenRequest_getUri_returnsUri();
        $this->givenUri_getPath_returnsValue($uri, self::REQUEST_URL);
        $this->givenImageSource_getCacheDirectory_returnsValue($imageSource, self::CACHE_DIRECTORY);
        $expectedTransformedRequest = $this->givenRequest_withUri_returnsRequestWithUri();

        $transformedRequest = $reducer->transformRequestForImageSource($this->request, $imageSource);

        $this->assertInstanceOf(RequestInterface::class, $transformedRequest);
        $this->assertRequest_getUri_isCalledOnce();
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageSource_getCacheDirectory_isCalledOnce($imageSource);
        $this->assertRequest_withUri_isCalledOnceWithUriWithPath(self::REDUCED_URL);
        $this->assertSame($expectedTransformedRequest, $transformedRequest);
    }

    private function givenRequest(): void
    {
        $this->request = \Phake::mock(RequestInterface::class);
    }

    private function givenRequest_getUri_returnsUri(): UriInterface
    {
        $uri = \Phake::mock(UriInterface::class);
        \Phake::when($this->request)->getUri()->thenReturn($uri);

        return $uri;
    }

    private function givenUri_getPath_returnsValue(UriInterface $uri, string $requestUrl): void
    {
        \Phake::when($uri)->getPath()->thenReturn($requestUrl);
    }

    private function assertRequest_getUri_isCalledOnce(): void
    {
        \Phake::verify($this->request, \Phake::times(1))->getUri();
    }

    private function assertUri_getPath_isCalledOnce(UriInterface $uri): void
    {
        \Phake::verify($uri, \Phake::times(1))->getPath();
    }

    private function assertImageSource_getCacheDirectory_isCalledOnce(AbstractImageSource $imageSource): void
    {
        \Phake::verify($imageSource, \Phake::times(1))->getCacheDirectory();
    }

    private function givenImageSource_getCacheDirectory_returnsValue(
        AbstractImageSource $imageSource,
        string $value
    ): void {
        \Phake::when($imageSource)->getCacheDirectory()->thenReturn(new DirectoryName($value));
    }

    private function assertRequest_withUri_isCalledOnceWithUriWithPath(string $path): void
    {
        /** @var UriInterface $uri */
        \Phake::verify($this->request, \Phake::times(1))
            ->withUri(\Phake::capture($uri));
        $this->assertEquals($path, $uri->getPath());
    }

    private function givenRequest_withUri_returnsRequestWithUri(): RequestInterface
    {
        $request = \Phake::mock(RequestInterface::class);
        \Phake::when($this->request)->withUri(\Phake::anyParameters())->thenReturn($request);

        return $request;
    }
}
