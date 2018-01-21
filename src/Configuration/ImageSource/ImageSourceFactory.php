<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration\ImageSource;

use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageSourceFactory implements ImageSourceFactoryInterface
{
    private const IMAGE_SOURCE_CLASS_MAP = [
        'filesystem' => FilesystemImageSource::class,
        'webdav' => WebDAVImageSource::class,
        'geomap' => GeoMapImageSource::class,
    ];

    private const CONSTRUCTOR_ARGUMENT_KEYS_MAP = [
        FilesystemImageSource::class => [
            'cache_directory',
            'storage_directory',
            'processor_type',
        ],
        WebDAVImageSource::class => [
            'cache_directory',
            'storage_directory',
            'processor_type',
            'driver_uri',
            'oauth_token',
        ],
        GeoMapImageSource::class => [
            'cache_directory',
            'driver',
            'api_key',
        ],
    ];

    /** @var EntityValidatorInterface */
    private $validator;

    /** @var array */
    private $configuration;

    public function __construct(EntityValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function createImageSourceByConfiguration(array $configuration): AbstractImageSource
    {
        $this->configuration = $configuration;

        $className = $this->getClassNameFromConfiguration();
        $arguments = $this->getConstructorArgumentsFromConfiguration($className);

        /** @var AbstractImageSource $imageSource */
        $imageSource = $this->createClass($className, $arguments);
        $this->validator->validateWithException($imageSource, InvalidConfigurationException::class);

        return $imageSource;
    }

    private function getClassNameFromConfiguration(): string
    {
        return self::IMAGE_SOURCE_CLASS_MAP[$this->configuration['type']];
    }

    private function getConstructorArgumentsFromConfiguration(string $className): array
    {
        $arguments = [];

        $argumentKeys = self::CONSTRUCTOR_ARGUMENT_KEYS_MAP[$className];
        foreach ($argumentKeys as $key) {
            $arguments[] = $this->configuration[$key];
        }

        return $arguments;
    }

    private function createClass(string $className, array $arguments)
    {
        $reflector = new \ReflectionClass($className);

        return $reflector->newInstanceArgs($arguments);
    }
}
