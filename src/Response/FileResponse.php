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

use Strider2038\ImgCache\Core\DeprecatedResponse;
use Strider2038\ImgCache\Exception\FileNotFoundException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FileResponse extends DeprecatedResponse
{
    /** @var string */
    protected $filename;
    
    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException("File '{$filename}' not found");
        }
        parent::__construct(self::HTTP_CODE_OK);

        $this->filename = $filename;

        $this->setHeader(self::HTTP_HEADER_CONTENT_TYPE, mime_content_type($filename));
    }
    
    protected function sendContent(): void
    {
        set_time_limit(0); // Reset time limit for big files
        $chunkSize = 8 * 1024 * 1024; // 8MB per chunk

        $stream = fopen($this->filename, 'r');

        while (!feof($stream)) {
            echo fread($stream, $chunkSize);
            flush();
        }

        fclose($stream);
    }
}
