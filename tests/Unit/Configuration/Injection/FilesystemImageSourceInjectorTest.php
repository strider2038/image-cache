<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration\Injection;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Configuration\Injection\FilesystemImageSourceInjector;
use Strider2038\ImgCache\Imaging\Extraction\ImageExtractorInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\FilesystemStorageDriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FilesystemImageSourceInjectorTest extends TestCase
{
    private const CACHE_DIRECTORY_ID = 'cache_directory_proxy';
    private const CACHE_DIRECTORY_VALUE = '/cache/';
    private const STORAGE_DIRECTORY_ID = 'storage_directory_proxy';
    private const STORAGE_DIRECTORY_VALUE = '/storage/';
    private const IMAGE_STORAGE_ID = 'image_storage_proxy';
    private const STORAGE_DRIVER_ID = 'storage_driver_proxy';
    private const IMAGE_EXTRACTOR_ID = 'image_extractor_proxy';
    private const FILESYSTEM_STORAGE_DRIVER_ID = 'filesystem_storage_driver';
    private const FILESYSTEM_STORAGE_ID = 'filesystem_storage';

    /** @var ContainerInterface */
    private $container;

    protected function setUp(): void
    {
        $this->container = \Phake::mock(ContainerInterface::class);
    }

    /**
     * @test
     * @param string $processorType
     * @param string $imageExtractorServiceId
     * @dataProvider processorTypeAndServicesProvider
     */
    public function injectSettingsToContainer_givenFilesystemImageSource_settingsInjectedToContainer(
        string $processorType,
        string $imageExtractorServiceId
    ): void {
        $imageSource = new FilesystemImageSource(
            self::CACHE_DIRECTORY_VALUE,
            self::STORAGE_DIRECTORY_VALUE,
            $processorType
        );
        $injector = new FilesystemImageSourceInjector($imageSource);
        $filesystemStorageDriver = $this->givenContainer_getWithId_returnsService(
            self::FILESYSTEM_STORAGE_DRIVER_ID,
            FilesystemStorageDriverInterface::class
        );
        $imageExtractor = $this->givenContainer_getWithId_returnsService(
            $imageExtractorServiceId,
            ImageExtractorInterface::class
        );
        $filesystemStorage = $this->givenContainer_getWithId_returnsService(
            self::FILESYSTEM_STORAGE_ID,
            ImageStorageInterface::class
        );

        $injector->injectSettingsToContainer($this->container);

        $this->assertContainer_set_isCalledOnceWithIdAndValue(self::CACHE_DIRECTORY_ID, self::CACHE_DIRECTORY_VALUE);
        $this->assertContainer_set_isCalledOnceWithIdAndValue(self::STORAGE_DIRECTORY_ID, self::STORAGE_DIRECTORY_VALUE);
        $this->assertContainer_get_isCalledOnceWithId(self::FILESYSTEM_STORAGE_DRIVER_ID);
        $this->assertContainer_set_isCalledOnceWithIdAndValue(self::STORAGE_DRIVER_ID, $filesystemStorageDriver);
        $this->assertContainer_get_isCalledOnceWithId($imageExtractorServiceId);
        $this->assertContainer_set_isCalledOnceWithIdAndValue(self::IMAGE_EXTRACTOR_ID, $imageExtractor);
        $this->assertContainer_get_isCalledOnceWithId(self::FILESYSTEM_STORAGE_ID);
        $this->assertContainer_set_isCalledOnceWithIdAndValue(self::IMAGE_STORAGE_ID, $filesystemStorage);
    }

    public function processorTypeAndServicesProvider(): array
    {
        return [
            ['copy', 'filesystem_original_image_extractor'],
            ['thumbnail', 'filesystem_thumbnail_image_extractor'],
        ];
    }

    private function assertContainer_set_isCalledOnceWithIdAndValue(string $id, $value): void
    {
        \Phake::verify($this->container, \Phake::times(1))->set($id, $value);
    }

    private function assertContainer_get_isCalledOnceWithId(string $id): void
    {
        \Phake::verify($this->container, \Phake::times(1))->get($id);
    }

    private function givenContainer_getWithId_returnsService(string $serviceId, string $serviceClass)
    {
        $service = \Phake::mock($serviceClass);
        \Phake::when($this->container)
            ->get($serviceId)
            ->thenReturn($service);

        return $service;
    }
}
