<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FunctionalTestCase extends TestCase
{
    protected const MIME_TYPE_JPEG = 'image/jpeg';
    protected const MIME_TYPE_PNG = 'image/png';

    protected const APPLICATION_DIRECTORY = __DIR__ . '/../..';
    protected const CONFIGURATION_DIRECTORY = self::APPLICATION_DIRECTORY . '/config';
    protected const RUNTIME_DIRECTORY = self::APPLICATION_DIRECTORY . '/runtime';

    protected const FILESOURCE_DIRECTORY = self::RUNTIME_DIRECTORY . '/tests/filesource';
    protected const WEB_DIRECTORY = self::RUNTIME_DIRECTORY . '/tests/web';
    protected const TEMPORARY_DIRECTORY = self::RUNTIME_DIRECTORY . '/tests/tmp';

    private const ASSETS_DIRECTORY = __DIR__ . '/../assets/';
    private const FILENAME_IMAGE_JPEG = self::ASSETS_DIRECTORY . 'sample/cat300.jpg';
    private const FILENAME_IMAGE_PNG = self::ASSETS_DIRECTORY . 'sample/rider.png';
    private const FILENAME_JSON = self::ASSETS_DIRECTORY . 'file.json';

    protected function setUp(): void
    {
        parent::setUp();

        exec('rm -rf ' . self::FILESOURCE_DIRECTORY);
        if (!mkdir(self::FILESOURCE_DIRECTORY, 0777, true)) {
            throw new \Exception('Cannot create test filesource directory');
        }

        exec('rm -rf ' . self::WEB_DIRECTORY);
        if (!mkdir(self::WEB_DIRECTORY, 0777, true)) {
            throw new \Exception('Cannot create test web directory');
        }

        exec('rm -rf ' . self::TEMPORARY_DIRECTORY);
        if (!mkdir(self::TEMPORARY_DIRECTORY, 0777, true)) {
            throw new \Exception('Cannot create test web directory');
        }
    }

    protected function loadContainer(string $filename): ContainerInterface
    {
        $container = new ContainerBuilder();
        $fileLocator = new FileLocator(self::CONFIGURATION_DIRECTORY);

        $loader = new YamlFileLoader($container, $fileLocator);
        $loader->load('testing/' . $filename);

        $container->setParameter('application.directory', self::APPLICATION_DIRECTORY);
        $container->setParameter('configuration_directory', self::CONFIGURATION_DIRECTORY);
        $container->setParameter('test.web_directory', self::WEB_DIRECTORY);
        $container->setParameter('test.filesource_directory', self::FILESOURCE_DIRECTORY);

        return $container;
    }

    private function givenAssetFile(string $assetFilename, string $copyFilename): void
    {
        if (file_exists($copyFilename)) {
            throw new \Exception(sprintf('File "%s" already exists', $copyFilename));
        }

        $dirname = dirname($copyFilename);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }

        if (!copy($assetFilename, $copyFilename)) {
            throw new \Exception(
                sprintf('Cannot copy "%s" to "%s"', $assetFilename, $copyFilename)
            );
        }
    }

    private function givenAssetFileContents(string $assetFilename): string
    {
        return file_get_contents($assetFilename);
    }

    protected function givenImageJpeg(string $filename): void
    {
        $this->givenAssetFile(self::FILENAME_IMAGE_JPEG, $filename);
    }

    protected function givenImageJpegContents(): string
    {
        return $this->givenAssetFileContents(self::FILENAME_IMAGE_JPEG);
    }

    protected function givenImagePng(string $filename): void
    {
        $this->givenAssetFile(self::FILENAME_IMAGE_PNG, $filename);
    }

    protected function givenJsonFile(string $filename): void
    {
        $this->givenAssetFile(self::FILENAME_JSON, $filename);
    }

    protected function assertFileHasMimeType(string $filename, string $expectedMime): void
    {
        $this->assertEquals($expectedMime, mime_content_type($filename));
    }
}