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
class TestImages
{
    const FILES_DIR = '/../assets/';
    
    public static function getFilename(string $name): string
    {
        $filename = __DIR__ . self::FILES_DIR . $name;
        if (!file_exists($filename)) {
            throw new \Exception("File '{$filename}' not found");
        }
        return $filename;
    }

}
