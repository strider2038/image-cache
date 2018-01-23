<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration\Injection;

use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemImageSourceInjector implements SettingsInjectorInterface
{
    private const CACHE_DIRECTORY_ID = 'cache_directory_proxy';
    private const STORAGE_DIRECTORY_ID = 'storage_directory_proxy';
    private const IMAGE_STORAGE_ID = 'image_storage_proxy';
    private const STORAGE_DRIVER_ID = 'storage_driver_proxy';
    private const IMAGE_EXTRACTOR_ID = 'image_extractor_proxy';

    /** @var FilesystemImageSource */
    private $imageSource;

    /** @var ContainerInterface */
    private $container;

    public function __construct(FilesystemImageSource $imageSource)
    {
        $this->imageSource = $imageSource;
    }

    public function injectSettingsToContainer(ContainerInterface $container): void
    {
        $this->container = $container;
        $this->injectParametersToContainer();
        $this->resolveDependenciesInContainer();
    }

    private function injectParametersToContainer(): void
    {
        $this->container->set(self::CACHE_DIRECTORY_ID, $this->imageSource->getCacheDirectory());
        $this->container->set(self::STORAGE_DIRECTORY_ID, $this->imageSource->getStorageDirectory());
    }

    private function resolveDependenciesInContainer(): void
    {
        $this->container->set(self::IMAGE_STORAGE_ID, '@' . $this->imageSource->getImageStorageServiceId());
        $this->container->set(self::STORAGE_DRIVER_ID, '@filesystem_storage_driver');
        $this->container->set(self::IMAGE_EXTRACTOR_ID, $this->getImageExtractorServiceId());
    }

    private function getImageExtractorServiceId(): string
    {
        $serviceId = '@filesystem_thumbnail_image_extractor';

        if ($this->imageSource->getProcessorType() === 'copy') {
            $serviceId = '@filesystem_original_image_extractor';
        }

        return $serviceId;
    }
}
