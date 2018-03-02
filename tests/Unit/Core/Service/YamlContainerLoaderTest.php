<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core\Service;

use Strider2038\ImgCache\Core\Service\YamlContainerLoader;
use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class YamlContainerLoaderTest extends FileTestCase
{
    /** @var FileLocatorInterface */
    private $fileLocator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileLocator = new FileLocator(self::TEST_CACHE_DIR);
    }

    /** @test */
    public function loadContainerFromFile_givenFilename_containerLoadedAndReturned(): void
    {
        $loader = new YamlContainerLoader($this->fileLocator);
        $this->givenYamlConfigFile();

        $container = $loader->loadContainerFromFile(self::FILE_YAML_CONFIG);

        $this->assertInstanceOf(ContainerBuilder::class, $container);
    }
}
