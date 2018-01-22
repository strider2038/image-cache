<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationSetter;
use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceCollection;
use Strider2038\ImgCache\Configuration\Injection\ImageSourceInjectorFactoryInterface;
use Strider2038\ImgCache\Configuration\Injection\SettingsInjectorInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\UriInterface;
use Strider2038\ImgCache\Imaging\Naming\DirectoryName;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConfigurationSetterTest extends TestCase
{
    private const ACCESS_CONTROL_TOKEN_KEY = 'access_control.token';
    private const ACCESS_CONTROL_TOKEN_VALUE = 'access_control_token';
    private const CACHED_IMAGE_QUALITY_KEY = 'image_parameters.quality';
    private const CACHED_IMAGE_QUALITY_VALUE = 75;

    /** @var ContainerInterface */
    private $container;

    /** @var RequestInterface */
    private $request;

    /** @var ImageSourceInjectorFactoryInterface */
    private $imageSourceInjectorFactory;

    protected function setUp(): void
    {
        $this->container = \Phake::mock(ContainerInterface::class);
        $this->request = \Phake::mock(RequestInterface::class);
        $this->imageSourceInjectorFactory = \Phake::mock(ImageSourceInjectorFactoryInterface::class);
    }

    /** @test */
    public function setConfigurationToContainer_givenConfiguration_propertiesSetToContainerAndDynamicDependebciesResolved(): void
    {
        $configurationSetter = $this->createConfigurationSetter();
        $imageSource = \Phake::mock(AbstractImageSource::class);
        $this->givenImageSource_getCacheDirectory_returnsCacheDirectory($imageSource, '/request/');
        $configuration = $this->givenConfigurationWithImageSource($imageSource);
        $uri = $this->givenRequest_getUri_returnsUri();
        $this->givenUri_getPath_returnsValue($uri, '/request/url');
        $settingsInjector = $this->givenImageSourceInjectorFactory_createSettingsInjectorForImageSource_returnsSettingsInjector();

        $configurationSetter->setConfigurationToContainer($configuration, $this->container);

        $this->assertParametersInjectedToContainer();
        $this->assertRequest_getUri_isCalledOnce();
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageSource_getCacheDirectory_isCalledOnce($imageSource);
        $this->assertImageSourceInjectorFactory_createSettingsInjectorForImageSource_isCalledOnceWithImageSource($imageSource);
        $this->assertSettingsInjector_injectSettingsToContainer_isCalledOnceWithContainer($settingsInjector);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Image source was not recognized
     */
    public function setConfigurationToContainer_givenConfiguration_dynamicDependenciesNotResolvedAndExceptionThrown(): void
    {
        $configurationSetter = $this->createConfigurationSetter();
        $imageSource = \Phake::mock(AbstractImageSource::class);
        $this->givenImageSource_getCacheDirectory_returnsCacheDirectory($imageSource, '/another-path/');
        $configuration = $this->givenConfigurationWithImageSource($imageSource);
        $uri = $this->givenRequest_getUri_returnsUri();
        $this->givenUri_getPath_returnsValue($uri, '/request/url');
        $this->givenImageSourceInjectorFactory_createSettingsInjectorForImageSource_returnsSettingsInjector();

        $configurationSetter->setConfigurationToContainer($configuration, $this->container);
    }

    private function createConfigurationSetter(): ConfigurationSetter
    {
        return new ConfigurationSetter(
            $this->request,
            $this->imageSourceInjectorFactory
        );
    }

    private function givenConfigurationWithImageSource(AbstractImageSource $imageSource): Configuration
    {
        return new Configuration(
            self::ACCESS_CONTROL_TOKEN_VALUE,
            self::CACHED_IMAGE_QUALITY_VALUE,
            new ImageSourceCollection([
                $imageSource
            ])
        );
    }

    private function givenRequest_getUri_returnsUri(): UriInterface
    {
        $uri = \Phake::mock(UriInterface::class);
        \Phake::when($this->request)->getUri()->thenReturn($uri);

        return $uri;
    }

    private function givenUri_getPath_returnsValue(UriInterface $uri, string $requestUrl): void
    {
        \Phake::when($uri)->getPath()->thenReturn($requestUrl);
    }

    private function assertParametersInjectedToContainer(): void
    {
        $this->assertContainer_setParameter_isCalledOnceWithNameAndValue(
            self::ACCESS_CONTROL_TOKEN_KEY,
            self::ACCESS_CONTROL_TOKEN_VALUE
        );
        $this->assertContainer_setParameter_isCalledOnceWithNameAndValue(
            self::CACHED_IMAGE_QUALITY_KEY,
            self::CACHED_IMAGE_QUALITY_VALUE
        );
    }

    private function assertContainer_setParameter_isCalledOnceWithNameAndValue(string $name, $value): void
    {
        \Phake::verify($this->container, \Phake::times(1))->setParameter($name, $value);
    }

    private function assertRequest_getUri_isCalledOnce(): void
    {
        \Phake::verify($this->request, \Phake::times(1))->getUri();
    }

    private function assertUri_getPath_isCalledOnce(UriInterface $uri): void
    {
        \Phake::verify($uri, \Phake::times(1))->getPath();
    }

    private function assertImageSource_getCacheDirectory_isCalledOnce(AbstractImageSource $imageSource): void
    {
        \Phake::verify($imageSource, \Phake::times(1))->getCacheDirectory();
    }

    private function givenImageSource_getCacheDirectory_returnsCacheDirectory(
        AbstractImageSource $imageSource,
        string $cacheDirectory
    ): void {
        \Phake::when($imageSource)->getCacheDirectory()->thenReturn(new DirectoryName($cacheDirectory));
    }

    private function assertImageSourceInjectorFactory_createSettingsInjectorForImageSource_isCalledOnceWithImageSource(
        AbstractImageSource $imageSource
    ): void {
        \Phake::verify($this->imageSourceInjectorFactory, \Phake::times(1))
            ->createSettingsInjectorForImageSource($imageSource);
    }

    private function assertSettingsInjector_injectSettingsToContainer_isCalledOnceWithContainer(
        \Strider2038\ImgCache\Configuration\Injection\SettingsInjectorInterface $settingsInjector
    ): void {
        \Phake::verify($settingsInjector, \Phake::times(1))->injectSettingsToContainer($this->container);
    }

    private function givenImageSourceInjectorFactory_createSettingsInjectorForImageSource_returnsSettingsInjector(): \Strider2038\ImgCache\Configuration\Injection\SettingsInjectorInterface
    {
        $settingsInjector = \Phake::mock(SettingsInjectorInterface::class);
        \Phake::when($this->imageSourceInjectorFactory)
            ->createSettingsInjectorForImageSource(\Phake::anyParameters())
            ->thenReturn($settingsInjector);

        return $settingsInjector;
    }
}
