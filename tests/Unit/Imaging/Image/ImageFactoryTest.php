<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Image;


use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Exception\InvalidImageException;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactory;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Imaging\Image\ImageParametersFactoryInterface;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class ImageFactoryTest extends TestCase
{
    private const DATA = 'data';

    /** @var ImageParametersFactoryInterface */
    private $parametersFactory;

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->parametersFactory = \Phake::mock(ImageParametersFactoryInterface::class);
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
    }

    /** @test */
    public function createImageFromStream_givenStreamAndNoParameters_imageWithCreatedParametersReturned(): void
    {
        $factory = $this->createImageFactory();
        $stream = $this->givenStream();
        $imageParameters = $this->givenImageParametersFactory_createImageParameters_returnsImageParameters();

        $image = $factory->createImageFromStream($stream);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertImageParametersFactory_createImageParameters_isCalledOnce();
        $this->assertSame($imageParameters, $image->getParameters());
        $this->assertSame($stream, $image->getData());
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            Image::class,
            InvalidImageException::class
        );
    }

    /** @test */
    public function createImageFromStream_givenStreamAndParameters_imageWithGivenParametersReturned(): void
    {
        $factory = $this->createImageFactory();
        $stream = $this->givenStream();
        $imageParameters = \Phake::mock(ImageParameters::class);

        $image = $factory->createImageFromStream($stream, $imageParameters);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertImageParametersFactory_createImageParameters_isNeverCalled();
        $this->assertSame($imageParameters, $image->getParameters());
        $this->assertSame($stream, $image->getData());
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            Image::class,
            InvalidImageException::class
        );
    }

    private function createImageFactory(): ImageFactory
    {
        return new ImageFactory($this->parametersFactory, $this->validator);
    }

    private function givenImageParametersFactory_createImageParameters_returnsImageParameters(): ImageParameters
    {
        $imageParameters = \Phake::mock(ImageParameters::class);
        \Phake::when($this->parametersFactory)->createImageParameters()->thenReturn($imageParameters);

        return $imageParameters;
    }

    private function assertImageParametersFactory_createImageParameters_isCalledOnce(): void
    {
        \Phake::verify($this->parametersFactory, \Phake::times(1))->createImageParameters();
    }

    private function assertImageParametersFactory_createImageParameters_isNeverCalled(): void
    {
        \Phake::verify($this->parametersFactory, \Phake::times(0))->createImageParameters();
    }

    private function givenStream(): StreamInterface
    {
        return \Phake::mock(StreamInterface::class);
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
