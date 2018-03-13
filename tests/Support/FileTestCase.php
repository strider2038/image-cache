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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileTestCase extends TestCase
{
    private const FILES_DIR = '/../assets/';
    protected const TEST_CACHE_DIR = '/tmp/imgcache-test';
    protected const IMAGE_CAT300 = 'sample/cat300.jpg';
    protected const IMAGE_CAT2000 = 'sample/cat2000.jpg';
    protected const IMAGE_RIDER_PNG = 'sample/rider.png';
    protected const IMAGE_BOX_JPG = 'box.jpg';
    protected const IMAGE_BOX_PNG = 'box.png';
    protected const IMAGE_POINT_PNG = 'point.png';
    protected const FILE_JSON = 'file.json';
    protected const FILE_JSON_CONTENTS = '{"isJson": true}';
    protected const FILE_YAML = 'yaml.yml';
    protected const FILE_YAML_CONFIG = 'config.yml';
    protected const FILE_WEBDAV_RESPONSE_XML = 'webdav-response.xml';
    protected const DIRECTORY_NAME = 'dirname';
    protected const FILENAME_NOT_EXIST = self::TEST_CACHE_DIR . '/not.exist';
    
    protected function setUp(): void
    {
        exec('rm -rf ' . self::TEST_CACHE_DIR);
        if (!mkdir(self::TEST_CACHE_DIR)) {
            throw new \Exception('Cannot create test directory');
        }
    }
    
    protected function givenDirectory(): string
    {
        $directory = self::TEST_CACHE_DIR . '/' . self::DIRECTORY_NAME;
        if (!mkdir($directory)) {
            throw new \Exception("Cannot create directory '{$directory}'");
        }

        return $directory;
    }

    protected function givenFile(): string
    {
        $filename = self::TEST_CACHE_DIR . '/' . self::FILE_JSON;
        $this->givenAssetFilename(self::FILE_JSON, $filename);

        return $filename;
    }

    protected function givenYamlFile(): string
    {
        $filename = self::TEST_CACHE_DIR . '/' . self::FILE_YAML;
        $this->givenAssetFilename(self::FILE_YAML, $filename);

        return $filename;
    }

    protected function givenYamlConfigFile(): string
    {
        $filename = self::TEST_CACHE_DIR . '/' . self::FILE_YAML_CONFIG;
        $this->givenAssetFilename(self::FILE_YAML_CONFIG, $filename);

        return $filename;
    }

    protected function givenAssetFilename(string $name, string $copyFilename = null): string
    {
        $filename = __DIR__ . self::FILES_DIR . $name;

        if (!file_exists($filename)) {
            throw new \Exception("File '{$filename}' not found");
        }

        if ($copyFilename !== null) {
            if (file_exists($copyFilename)) {
                throw new \Exception("File {$copyFilename} already exists");
            }
            if (!copy($filename, $copyFilename)) {
                throw new \Exception("Cannot copy '{$filename}' to '{$copyFilename}'");
            }
        }

        return $filename;
    }
}
