<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Naming;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\UriInterface;
use Strider2038\ImgCache\Exception\InvalidRequestValueException;
use Strider2038\ImgCache\Imaging\Naming\ImageFilename;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactory;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class ImageFilenameFactoryTest extends TestCase
{
    private const URI_PATH = 'uri_path';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
    }

    /**
     * @test
     * @dataProvider filenameProvider
     * @param string $uriPath
     * @param string $expectedFilenameValue
     * @throws \Strider2038\ImgCache\Exception\InvalidRequestValueException
     */
    public function createImageFilenameFromRequest_givenRequest_ImageFilenameCreatedAndReturned(
        string $uriPath,
        string $expectedFilenameValue
    ): void {
        $factory = $this->createImageFilenameFactory();
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUri($request);
        $this->givenUri_getPath_returnsString($uri, $uriPath);

        $filename = $factory->createImageFilenameFromRequest($request);

        $this->assertInstanceOf(ImageFilename::class, $filename);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertEquals($expectedFilenameValue, $filename->getValue());
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            ImageFilename::class,
            InvalidRequestValueException::class
        );
    }

    public function filenameProvider(): array
    {
        return [
            [self::URI_PATH, self::URI_PATH],
            ['/' . self::URI_PATH, self::URI_PATH],
        ];
    }

    private function createImageFilenameFactory(): ImageFilenameFactory
    {
        return new ImageFilenameFactory($this->validator);
    }

    private function givenRequest(): RequestInterface
    {
        return \Phake::mock(RequestInterface::class);
    }

    private function assertRequest_getUri_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getUri();
    }

    private function assertUri_getPath_isCalledOnce(UriInterface $uri): void
    {
        \Phake::verify($uri, \Phake::times(1))->getPath();
    }

    private function givenRequest_getUri_returnsUri($request): UriInterface
    {
        $uri = \Phake::mock(UriInterface::class);
        \Phake::when($request)->getUri()->thenReturn($uri);

        return $uri;
    }

    private function givenUri_getPath_returnsString(UriInterface $uri, string $path): void
    {
        \Phake::when($uri)->getPath()->thenReturn($path);
    }

    private function assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
        string $entityClass,
        string $exceptionClass
    ): void {
        \Phake::verify($this->validator, \Phake::times(1))
            ->validateWithException(\Phake::capture($entity), \Phake::capture($exception));
        $this->assertInstanceOf($entityClass, $entity);
        $this->assertEquals($exceptionClass, $exception);
    }
}
