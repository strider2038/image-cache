<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Response;

use Strider2038\ImgCache\Core\Response;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileResponse extends Response
{
    protected $filename;
    
    public function __construct(string $filename)
    {
        parent::__construct(self::HTTP_CODE_OK);
        $this->filename = $filename;
    }
    
    protected function sendContent(): void
    {
        
    }
}
