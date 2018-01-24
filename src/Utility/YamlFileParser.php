<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Utility;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YamlFileParser implements ConfigurationFileParserInterface
{
    /** @var FileLocatorInterface */
    private $fileLocator;

    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    public function parseConfigurationFile(string $filename): array
    {
        $absoluteFilename = $this->fileLocator->locate($filename);

        return Yaml::parseFile($absoluteFilename) ?? [];
    }
}
