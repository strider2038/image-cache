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

use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceCollection;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Exception\InvalidRouteException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ConfigurationSetter implements ConfigurationSetterInterface
{
    private const ACCESS_CONTROL_TOKEN = 'access_control.token';
    private const IMAGE_PARAMETERS_QUALITY = 'image_parameters.quality';

    /** @var RequestInterface */
    private $request;

    /** @var ImageSourceInjectorFactoryInterface */
    private $imageSourceInjectorFactory;

    /** @var ContainerInterface */
    private $container;

    /** @var bool */
    private $imageSourceDetected;

    public function __construct(
        RequestInterface $request,
        ImageSourceInjectorFactoryInterface $imageSourceInjectorFactory
    ) {
        $this->request = $request;
        $this->imageSourceInjectorFactory = $imageSourceInjectorFactory;
    }

    public function setConfigurationToContainer(Configuration $configuration, ContainerInterface $container): void
    {
        $this->container = $container;
        $this->imageSourceDetected = false;

        $this->injectParametersToContainer($configuration);
        $this->injectImageSourceParametersToContainer($configuration->getSourceCollection());

        if (!$this->imageSourceDetected) {
            throw new InvalidRouteException('Image source was not recognized.');
        }
    }

    private function injectParametersToContainer(Configuration $configuration): void
    {
        $this->container->setParameter(self::ACCESS_CONTROL_TOKEN, $configuration->getAccessControlToken());
        $this->container->setParameter(self::IMAGE_PARAMETERS_QUALITY, $configuration->getCachedImageQuality());
    }

    private function injectImageSourceParametersToContainer(ImageSourceCollection $imageSourceCollection): void
    {
        $uri = $this->request->getUri();
        $path = $uri->getPath();

        /** @var AbstractImageSource $imageSource */
        foreach ($imageSourceCollection as $imageSource) {
            $routeDetectionPattern = $this->getRouteDetectionPattern($imageSource);
            if (preg_match($routeDetectionPattern, $path)) {
                $this->imageSourceDetected = true;
                $this->injectImageSourceSettingsToContainer($imageSource);
            }
        }
    }

    private function getRouteDetectionPattern(AbstractImageSource $imageSource): string
    {
        $cacheDirectory = $imageSource->getCacheDirectory();
        $routeDetectionPattern = sprintf('/^%s.*$/', str_replace('/', '\\/', $cacheDirectory));

        return $routeDetectionPattern;
    }

    private function injectImageSourceSettingsToContainer(AbstractImageSource $imageSource): void
    {
        $settingsInjector = $this->imageSourceInjectorFactory->createSettingsInjectorForImageSource($imageSource);
        $settingsInjector->injectSettingsToContainer($this->container);
    }
}
