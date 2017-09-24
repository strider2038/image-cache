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
    protected const APPLICATION_DIRECTORY = __DIR__ . '/../app';
    protected const FILESOURCE_DIRECTORY = self::APPLICATION_DIRECTORY . '/filesource';
    protected const WEB_DIRECTORY = self::APPLICATION_DIRECTORY . '/web';
    protected const RUNTIME_DIRECTORY = self::APPLICATION_DIRECTORY . '/runtime';

    private const ASSETS_DIRECTORY = __DIR__ . '/../assets/';
    private const IMAGE_JPEG_FILENAME = self::ASSETS_DIRECTORY . 'sample/cat300.jpg';

    protected function setUp()
    {
        parent::setUp();
        exec('rm -rf ' . self::FILESOURCE_DIRECTORY);
        if (!mkdir(self::FILESOURCE_DIRECTORY)) {
            throw new \Exception('Cannot create test filesource directory');
        }
        exec('rm -rf ' . self::WEB_DIRECTORY);
        if (!mkdir(self::WEB_DIRECTORY)) {
            throw new \Exception('Cannot create test web directory');
        }
        exec('rm -rf ' . self::RUNTIME_DIRECTORY);
        if (!mkdir(self::RUNTIME_DIRECTORY)) {
            throw new \Exception('Cannot create test runtime directory');
        }
    }

    protected function loadContainer(string $filename): ContainerInterface
    {
        $container = new ContainerBuilder();
        $fileLocator = new FileLocator(self::APPLICATION_DIRECTORY);
        $loader = new YamlFileLoader($container, $fileLocator);
        $loader->load('config/' . $filename);
        $container->setParameter('test.webDirectory', self::WEB_DIRECTORY);
        $container->setParameter('test.filesourceDirectory', self::FILESOURCE_DIRECTORY);
        $container->setParameter('test.runtimeDirectory', self::RUNTIME_DIRECTORY);

        return $container;
    }

    protected function givenImageJpeg(string $filename): void
    {
        if (file_exists($filename)) {
            throw new \Exception("File '{$filename}' already exists");
        }
        $dirname = dirname($filename);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }
        if (!copy(self::IMAGE_JPEG_FILENAME, $filename)) {
            throw new \Exception(
                sprintf("Cannot copy '%s' to '%s'", self::IMAGE_JPEG_FILENAME, $filename)
            );
        }
    }
}