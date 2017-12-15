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
use Strider2038\ImgCache\Imaging\Naming\ImageFilename;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactory;
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationFormatterInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ImageFilenameFactoryTest extends TestCase
{
    private const URI_PATH = 'uri_path';

    /** @var ModelValidatorInterface */
    private $validator;

    /** @var ViolationFormatterInterface */
    private $violationFormatter;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(ModelValidatorInterface::class);
        $this->violationFormatter = \Phake::mock(ViolationFormatterInterface::class);
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
        $factory = new ImageFilenameFactory($this->validator, $this->violationFormatter);
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUri($request);
        $this->givenUri_getPath_returnsString($uri, $uriPath);
        $violations = $this->givenValidator_validateModel_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 0);

        $filename = $factory->createImageFilenameFromRequest($request);

        $this->assertInstanceOf(ImageFilename::class, $filename);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertEquals($expectedFilenameValue, $filename->getValue());
        $this->assetValidator_validateModel_isCalledOnceWithAnyParameter();
    }

    public function filenameProvider(): array
    {
        return [
            [self::URI_PATH, self::URI_PATH],
            ['/' . self::URI_PATH, self::URI_PATH],
        ];
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Given invalid image filename
     */
    public function createImageFilenameFromRequest_givenInvalidRequest_invalidRequestValueExceptionThrown(): void
    {
        $factory = new ImageFilenameFactory($this->validator, $this->violationFormatter);
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUri($request);
        $this->givenUri_getPath_returnsString($uri, self::URI_PATH);
        $violations = $this->givenValidator_validateModel_returnViolations();
        $this->givenViolations_count_returnsCount($violations, 1);

        $factory->createImageFilenameFromRequest($request);
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

    private function assetValidator_validateModel_isCalledOnceWithAnyParameter(): void
    {
        \Phake::verify($this->validator, \Phake::times(1))->validateModel(\Phake::anyParameters());
    }

    private function givenValidator_validateModel_returnViolations(): ConstraintViolationListInterface
    {
        $violations = \Phake::mock(ConstraintViolationListInterface::class);
        \Phake::when($this->validator)->validateModel(\Phake::anyParameters())->thenReturn($violations);

        return $violations;
    }

    private function givenViolations_count_returnsCount(ConstraintViolationListInterface $violations, int $count): void
    {
        \Phake::when($violations)->count()->thenReturn($count);
    }
}
