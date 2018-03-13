<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core\Service;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YamlContainerLoader implements FileContainerLoaderInterface
{
    /** @var FileLocatorInterface */
    private $fileLocator;

    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    public function loadContainerFromFile(string $filename): ContainerInterface
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, $this->fileLocator);
        $loader->load($filename);

        return $container;
    }
}
