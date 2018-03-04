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
use Strider2038\ImgCache\Core\Http\UriInterface;
use Strider2038\ImgCache\Imaging\Naming\DirectoryName;
use Strider2038\ImgCache\Service\Image\ImageSourceDetector;

class ImageSourceDetectorTest extends TestCase
{
    private const CACHE_DIRECTORY = '/cache/directory';
    private const REQUEST_URL_MATCHING_CACHE_DIRECTORY = self::CACHE_DIRECTORY . '/url';
    private const REQUEST_URL_NOT_MATCHING_CACHE_DIRECTORY = '/request/url';

    /** @var AbstractImageSource */
    private $imageSource;

    /** @var RequestInterface */
    private $request;

    protected function setUp(): void
    {
        $this->imageSource = \Phake::mock(AbstractImageSource::class);
    }

    /** @test */
    public function detectImageSourceByRequest_givenImageSourcesAndRequest_imageSourceFoundAndReturned(): void
    {
        $detector = $this->createImageSourceDetectorWithGivenImageSource();
        $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUri();
        $this->givenUri_getPath_returnsValue($uri, self::REQUEST_URL_MATCHING_CACHE_DIRECTORY);
        $this->givenImageSource_getCacheDirectory_returnsDirectoryName(self::CACHE_DIRECTORY);

        $imageSource = $detector->detectImageSourceByRequest($this->request);

        $this->assertInstanceOf(AbstractImageSource::class, $imageSource);
        $this->assertRequest_getUri_isCalledOnce();
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageSource_getCacheDirectory_isCalledOnce();
        $this->assertSame($this->imageSource, $imageSource);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\ImageSourceNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Image source was not found for given url path
     */
    public function detectImageSourceByRequest_givenImageSourcesAndRequest_imageSourceNotFoundAndExceptionThrown(): void
    {
        $detector = $this->createImageSourceDetectorWithGivenImageSource();
        $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUri();
        $this->givenUri_getPath_returnsValue($uri, self::REQUEST_URL_NOT_MATCHING_CACHE_DIRECTORY);
        $this->givenImageSource_getCacheDirectory_returnsDirectoryName(self::CACHE_DIRECTORY);

        $detector->detectImageSourceByRequest($this->request);
    }

    private function createImageSourceDetectorWithGivenImageSource(): ImageSourceDetector
    {
        return new ImageSourceDetector(
            new ImageSourceCollection(
                [
                    $this->imageSource
                ]
            )
        );
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

    private function assertImageSource_getCacheDirectory_isCalledOnce(): void
    {
        \Phake::verify($this->imageSource, \Phake::times(1))
            ->getCacheDirectory();
    }

    private function givenImageSource_getCacheDirectory_returnsDirectoryName(string $cacheDirectory): void
    {
        \Phake::when($this->imageSource)
            ->getCacheDirectory()
            ->thenReturn(new DirectoryName($cacheDirectory));
    }
}
