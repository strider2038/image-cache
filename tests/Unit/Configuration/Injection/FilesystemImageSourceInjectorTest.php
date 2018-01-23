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

        $injector->injectSettingsToContainer($this->container);

        $this->assertContainer_set_isCalledOnceWithIdAndValue(self::CACHE_DIRECTORY_ID, self::CACHE_DIRECTORY_VALUE);
        $this->assertContainer_set_isCalledOnceWithIdAndValue(self::STORAGE_DIRECTORY_ID, self::STORAGE_DIRECTORY_VALUE);
        $this->assertContainer_set_isCalledOnceWithIdAndValue(self::IMAGE_STORAGE_ID, '@filesystem_storage');
        $this->assertContainer_set_isCalledOnceWithIdAndValue(self::STORAGE_DRIVER_ID, '@filesystem_storage_driver');
        $this->assertContainer_set_isCalledOnceWithIdAndValue(self::IMAGE_EXTRACTOR_ID, $imageExtractorServiceId);
    }

    public function processorTypeAndServicesProvider(): array
    {
        return [
            ['copy', '@filesystem_original_image_extractor'],
            ['thumbnail', '@filesystem_thumbnail_image_extractor'],
        ];
    }

    private function assertContainer_set_isCalledOnceWithIdAndValue(string $id, $value): void
    {
        \Phake::verify($this->container, \Phake::times(1))->set($id, $value);
    }

    private function assertContainer_setParameter_isCalledOnceWithNameAndValue(string $name, $value): void
    {
        \Phake::verify($this->container, \Phake::times(1))->setParameter($name, $value);
    }
}
