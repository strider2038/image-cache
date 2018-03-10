<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;
use Strider2038\ImgCache\Imaging\FilesystemImageStorageFactory;
use Strider2038\ImgCache\Imaging\GeoMapImageStorageFactory;
use Strider2038\ImgCache\Imaging\ImageStorageFactory;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\WebDAVImageStorageFactory;

class ImageStorageFactoryTest extends TestCase
{
    /** @var FilesystemImageStorageFactory */
    private $filesystemImageStorageFactory;
    /** @var WebDAVImageStorageFactory */
    private $webdavImageStorageFactory;
    /** @var GeoMapImageStorageFactory */
    private $geoMapImageStorageFactory;

    protected function setUp(): void
    {
        $this->filesystemImageStorageFactory = \Phake::mock(FilesystemImageStorageFactory::class);
        $this->webdavImageStorageFactory = \Phake::mock(WebDAVImageStorageFactory::class);
        $this->geoMapImageStorageFactory = \Phake::mock(GeoMapImageStorageFactory::class);
    }

    /**
     * @test
     * @dataProvider ConcreteImageStorageFactoryAndImageSourceClassProvider
     * @param $concreteImageStorageFactoryVariable
     * @param string $imageSourceClass
     */
    public function createImageStorageForImageSource_givenImageSource_imageStorageCreatedByConcreteFactoryAndReturned(
        $concreteImageStorageFactoryVariable,
        string $imageSourceClass
    ): void {
        $factory = $this->createImageStorageFactory();
        $concreteImageStorageFactory = $this->$concreteImageStorageFactoryVariable;
        $imageSource = \Phake::mock($imageSourceClass);
        $createdImageStorage = $this->givenConcreteImageStorageFactory_createImageStorageForImageSource_returnsImageStorage($concreteImageStorageFactory);

        $imageStorage = $factory->createImageStorageForImageSource($imageSource);

        $this->assertInstanceOf(ImageStorageInterface::class, $imageStorage);
        $this->assertConcreteImageStorageFactory_createImageStorageForImageSource_isCalledOnceWithImageSource($concreteImageStorageFactory, $imageSource);
        $this->assertSame($createdImageStorage, $imageStorage);
    }

    public function ConcreteImageStorageFactoryAndImageSourceClassProvider(): array
    {
        return [
            [
                'filesystemImageStorageFactory',
                FilesystemImageSource::class
            ],
            [
                'webdavImageStorageFactory',
                WebDAVImageSource::class
            ],
            [
                'geoMapImageStorageFactory',
                GeoMapImageSource::class
            ],
        ];
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Cannot find factory for given image source class
     */
    public function createImageStorageForImageSource_givenImageSource_concreteFactoryNotFoundAndInvalidConfigurationExceptionThrown(): void
    {
        $factory = $this->createImageStorageFactory();
        $imageSource = \Phake::mock(AbstractImageSource::class);

        $factory->createImageStorageForImageSource($imageSource);
    }

    private function createImageStorageFactory(): ImageStorageFactory
    {
        return new ImageStorageFactory(
            $this->filesystemImageStorageFactory,
            $this->webdavImageStorageFactory,
            $this->geoMapImageStorageFactory
        );
    }

    private function assertConcreteImageStorageFactory_createImageStorageForImageSource_isCalledOnceWithImageSource(
        $concreteImageStorageFactory,
        AbstractImageSource $imageSource
    ): void {
        \Phake::verify($concreteImageStorageFactory, \Phake::times(1))
            ->createImageStorageForImageSource($imageSource);
    }

    private function givenConcreteImageStorageFactory_createImageStorageForImageSource_returnsImageStorage(
        $concreteImageStorageFactory
    ): ImageStorageInterface {
        $createdImageStorage = \Phake::mock(ImageStorageInterface::class);
        \Phake::when($concreteImageStorageFactory)
            ->createImageStorageForImageSource(\Phake::anyParameters())
            ->thenReturn($createdImageStorage);

        return $createdImageStorage;
    }
}
