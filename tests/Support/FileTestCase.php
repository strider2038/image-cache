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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileTestCase extends ProjectTestCase
{
    private const FILES_DIR = '/../assets/';

    const TEST_CACHE_DIR = '/tmp/imgcache-test';

    const IMAGE_CAT300 = 'cat300.jpg';
    const IMAGE_CAT2000 = 'cat2000.jpg';
    const IMAGE_RIDER_PNG = 'rider.png';
    const IMAGE_BOX_JPG = 'box.jpg';
    const IMAGE_BOX_PNG = 'box.png';
    const FILE_JSON = 'file.json';
    
    protected function setUp() 
    {
        exec('rm -rf ' . self::TEST_CACHE_DIR);
        if (!mkdir(self::TEST_CACHE_DIR)) {
            throw new \Exception('Cannot create test directory');
        }
    }
    
    protected function tearDown()
    {
        //exec('rm -rf ' . self::TEST_CACHE_DIR);
    }

    public function givenFile(string $name, string $copyFilename = null): string
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
