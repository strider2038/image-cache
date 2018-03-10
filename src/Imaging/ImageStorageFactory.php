<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging;

use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;
use Strider2038\ImgCache\Exception\InvalidConfigurationException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageStorageFactory implements ImageStorageFactoryInterface
{
    /** @var FilesystemImageStorageFactory */
    private $filesystemImageStorageFactory;
    /** @var WebDAVImageStorageFactory */
    private $webdavImageStorageFactory;
    /** @var GeoMapImageStorageFactory */
    private $geoMapImageStorageFactory;

    public function __construct(
        FilesystemImageStorageFactory $filesystemImageStorageFactory,
        WebDAVImageStorageFactory $webdavImageStorageFactory,
        GeoMapImageStorageFactory $geoMapImageStorageFactory
    ) {
        $this->filesystemImageStorageFactory = $filesystemImageStorageFactory;
        $this->webdavImageStorageFactory = $webdavImageStorageFactory;
        $this->geoMapImageStorageFactory = $geoMapImageStorageFactory;
    }

    public function createImageStorageForImageSource(AbstractImageSource $imageSource): ImageStorageInterface
    {
        $imageStorage = $this->createImageStorageByConcreteFactory($imageSource);

        if ($imageStorage === null) {
            throw new InvalidConfigurationException(
                sprintf(
                    'Cannot find factory for given image source class "%s".',
                    \get_class($imageSource)
                )
            );
        }

        return $imageStorage;
    }

    private function createImageStorageByConcreteFactory(AbstractImageSource $imageSource): ? ImageStorageInterface
    {
        $imageStorage = null;

        if ($imageSource instanceof WebDAVImageSource) {
            $imageStorage = $this->webdavImageStorageFactory->createImageStorageForImageSource($imageSource);
        } else if ($imageSource instanceof FilesystemImageSource) {
            $imageStorage = $this->filesystemImageStorageFactory->createImageStorageForImageSource($imageSource);
        } else if ($imageSource instanceof GeoMapImageSource) {
            $imageStorage = $this->geoMapImageStorageFactory->createImageStorageForImageSource($imageSource);
        }

        return $imageStorage;
    }
}
