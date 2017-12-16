<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core\Http;

use Strider2038\ImgCache\Core\Streaming\StreamInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResponseSender implements ResponseSenderInterface
{
    const CHUNK_SIZE = 8 * 1024 * 1024;

    public function send(ResponseInterface $response): void
    {
        header(sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion()->getValue(),
            $response->getStatusCode()->getValue(),
            $response->getReasonPhrase()
        ));

        $this->sendHeaders($response->getHeaders());
        $this->sendContent($response->getBody());
    }

    private function sendHeaders(HeaderCollection $headers): void
    {
        foreach ($headers as $name => $values) {
            $replace = true;
            foreach ($values as $value) {
                header("$name: $value", $replace);
                $replace = false;
            }
        }
    }

    private function sendContent(StreamInterface $stream): void
    {
        while (!$stream->eof()) {
            echo $stream->read(self::CHUNK_SIZE);
            flush();
        }

        $stream->close();
    }
}
