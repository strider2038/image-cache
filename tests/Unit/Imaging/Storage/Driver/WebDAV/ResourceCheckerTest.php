<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Driver\WebDAV;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Enum\WebDAVResourceTypeEnum;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceChecker;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceProperties;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourcePropertiesCollection;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourcePropertiesGetterInterface;

class ResourceCheckerTest extends TestCase
{
    private const RESOURCE_URI = 'resource_uri';

    /** @var ResourcePropertiesGetterInterface */
    private $propertiesGetter;

    protected function setUp(): void
    {
        $this->propertiesGetter = \Phake::mock(ResourcePropertiesGetterInterface::class);
    }

    /**
     * @test
     * @dataProvider resourcePropertiesAndFileExistsProvider
     * @param string $method
     * @param ResourcePropertiesCollection $propertiesCollection
     * @param bool $expectedResult
     */
    public function givenMethod_givenResourceUriAndResourcePropertiesCollection_boolReturned(
        string $method,
        ResourcePropertiesCollection $propertiesCollection,
        bool $expectedResult
    ): void {
        $checker = $this->createResourceChecker();
        $this->givenResourcePropertiesGetter_getResourcePropertiesCollection_returnsResourcePropertiesCollection($propertiesCollection);

        $actualResult = $checker->$method(self::RESOURCE_URI);

        $this->assertResourcePropertiesGetter_getResourcePropertiesCollection_isCalledOnceWithRequestUri(self::RESOURCE_URI);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function resourcePropertiesAndFileExistsProvider(): array
    {
        return [
            [
                'isFile',
                new ResourcePropertiesCollection(),
                false,
            ],
            [
                'isFile',
                $this->givenResourcePropertiesCollectionForDirectory(),
                false,
            ],
            [
                'isFile',
                $this->givenResourcePropertiesCollectionForFile(),
                true,
            ],
            [
                'isDirectory',
                new ResourcePropertiesCollection(),
                false,
            ],
            [
                'isDirectory',
                $this->givenResourcePropertiesCollectionForDirectory(),
                true,
            ],
            [
                'isDirectory',
                $this->givenResourcePropertiesCollectionForFile(),
                false,
            ],
        ];
    }

    private function createResourceChecker(): ResourceChecker
    {
        return new ResourceChecker($this->propertiesGetter);
    }

    private function assertResourcePropertiesGetter_getResourcePropertiesCollection_isCalledOnceWithRequestUri(
        string $resourceUri
    ): void {
        \Phake::verify($this->propertiesGetter, \Phake::times(1))->getResourcePropertiesCollection($resourceUri);
    }

    private function givenResourcePropertiesGetter_getResourcePropertiesCollection_returnsResourcePropertiesCollection(
        ResourcePropertiesCollection $resourcePropertiesCollection
    ): void {
        \Phake::when($this->propertiesGetter)
            ->getResourcePropertiesCollection(\Phake::anyParameters())
            ->thenReturn($resourcePropertiesCollection);
    }

    private function givenResourcePropertiesCollectionForDirectory(): ResourcePropertiesCollection
    {
        $properties = \Phake::mock(ResourceProperties::class);
        $resourceType = new WebDAVResourceTypeEnum(WebDAVResourceTypeEnum::DIRECTORY);
        \Phake::when($properties)->getResourceType()->thenReturn($resourceType);

        return new ResourcePropertiesCollection([$properties]);
    }

    private function givenResourcePropertiesCollectionForFile(): ResourcePropertiesCollection
    {
        $properties = \Phake::mock(ResourceProperties::class);
        $resourceType = new WebDAVResourceTypeEnum(WebDAVResourceTypeEnum::FILE);
        \Phake::when($properties)->getResourceType()->thenReturn($resourceType);

        return new ResourcePropertiesCollection([$properties]);
    }
}
