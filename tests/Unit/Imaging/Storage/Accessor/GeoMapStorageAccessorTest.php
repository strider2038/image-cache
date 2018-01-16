<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Accessor;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\GeoMap\GeoMapParameters;
use Strider2038\ImgCache\Imaging\Storage\Accessor\GeoMapStorageAccessor;
use Strider2038\ImgCache\Imaging\Storage\Converter\GeoMapParametersConverterInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\ApiStorageDriverInterface;

class GeoMapStorageAccessorTest extends TestCase
{
    /** @var GeoMapParametersConverterInterface */
    private $parametersConverter;

    /** @var ApiStorageDriverInterface */
    private $storageDriver;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    protected function setUp(): void
    {
        $this->parametersConverter = \Phake::mock(GeoMapParametersConverterInterface::class);
        $this->storageDriver = \Phake::mock(ApiStorageDriverInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
    }

    /** @test */
    public function getImage_givenGeoMapParameters_imageRequestedInStorageAndReturned(): void
    {
        $accessor = $this->createGeoMapStorageAccessor();
        $parameters = new GeoMapParameters();
        $query = $this->givenParametersConverter_convertGeoMapParametersToQuery_returnsQueryParameterCollection();
        $imageContents = $this->givenStorageDriver_getImageContents_returnsStream();
        $expectedImage = $this->givenImageFactory_createImageFromStream_returnsImage();

        $image = $accessor->getImage($parameters);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertParametersConverter_convertGeoMapParametersToQuery_isCalledOnceWithGeoMapParameters($parameters);
        $this->assertStorageDriver_getImageContents_isCalledOnceWithQuery($query);
        $this->assertImageFactory_createImageFromStream_isCalledOnceWithStream($imageContents);
        $this->assertSame($expectedImage, $image);
    }

    private function createGeoMapStorageAccessor(): GeoMapStorageAccessor
    {
        return new GeoMapStorageAccessor(
            $this->parametersConverter,
            $this->storageDriver,
            $this->imageFactory
        );
    }

    private function givenParametersConverter_convertGeoMapParametersToQuery_returnsQueryParameterCollection(): QueryParameterCollection
    {
        $query = \Phake::mock(QueryParameterCollection::class);
        \Phake::when($this->parametersConverter)
            ->convertGeoMapParametersToQuery(\Phake::anyParameters())
            ->thenReturn($query);

        return $query;
    }

    private function givenStorageDriver_getImageContents_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->storageDriver)->getImageContents(\Phake::anyParameters())->thenReturn($stream);

        return $stream;
    }

    private function givenImageFactory_createImageFromStream_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createImageFromStream(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }

    private function assertParametersConverter_convertGeoMapParametersToQuery_isCalledOnceWithGeoMapParameters(
        GeoMapParameters $parameters
    ): void {
        \Phake::verify($this->parametersConverter, \Phake::times(1))
            ->convertGeoMapParametersToQuery($parameters);
    }

    private function assertStorageDriver_getImageContents_isCalledOnceWithQuery(QueryParameterCollection $query): void
    {
        \Phake::verify($this->storageDriver, \Phake::times(1))->getImageContents($query);
    }

    private function assertImageFactory_createImageFromStream_isCalledOnceWithStream(StreamInterface $stream): void
    {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createImageFromStream($stream);
    }
}
