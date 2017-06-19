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
    const TEST_DIR = '/tmp/imgcache-test';
    const IMAGE_CAT300 = 'cat300.jpg';
    const IMAGE_CAT2000 = 'cat2000.jpg';
    
    public function setUp() 
    {
        exec('rm -rf ' . self::TEST_DIR);
        mkdir(self::TEST_DIR);
    }
    
    public function tearDown()
    {
        exec('rm -rf ' . self::TEST_DIR);
    }

    public function getImageFilename(string $name): string
    {
        return TestImages::getFilename($name);
    }
}
