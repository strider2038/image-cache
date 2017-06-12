<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging;

use Strider2038\ImgCache\Exception\FileNotFoundException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Image
{
    private $filename;
    
    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException("File '{$filename}' not found");
        }
        $this->filename = $filename;
    }
    
    public function getData()
    {
        
    }
}
