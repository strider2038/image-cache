<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration;

use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceCollection;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ConfigurationFactory implements ConfigurationFactoryInterface
{
    /** @var ImageSourceFactoryInterface */
    private $imageSourceFactory;

    public function __construct(ImageSourceFactoryInterface $imageSourceFactory)
    {
        $this->imageSourceFactory = $imageSourceFactory;
    }

    public function createConfiguration(array $configuration): Configuration
    {
        $imageSourceCollection = new ImageSourceCollection();

        foreach ($configuration['image_sources'] as $sourceConfiguration) {
            $imageSource = $this->imageSourceFactory->createImageSourceByConfiguration($sourceConfiguration);
            $imageSourceCollection->add($imageSource);
        }

        return new Configuration(
            $configuration['access_control_token'],
            $configuration['cached_image_quality'],
            $imageSourceCollection
        );
    }
}
