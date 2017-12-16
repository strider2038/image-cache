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

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\Streaming\NullStream;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;
use Strider2038\ImgCache\Enum\HttpProtocolVersionEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Message implements MessageInterface
{
    /** @var HttpProtocolVersionEnum */
    protected $protocolVersion;

    /** @var HeaderCollection */
    protected $headers;

    /** @var StreamInterface */
    protected $body;

    public function __construct()
    {
        $this->protocolVersion = new HttpProtocolVersionEnum(HttpProtocolVersionEnum::V1_1);
        $this->headers = new HeaderCollection();
        $this->body = new NullStream();
    }

    public function getProtocolVersion(): HttpProtocolVersionEnum
    {
        return $this->protocolVersion;
    }

    public function setProtocolVersion(HttpProtocolVersionEnum $protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
    }

    public function getHeaders(): HeaderCollection
    {
        return $this->headers;
    }

    public function setHeaders(HeaderCollection $headers): void
    {
        $this->headers = $headers;
    }

    public function hasHeader(HttpHeaderEnum $name): bool
    {
        return $this->headers->containsKey($name);
    }

    public function getHeader(HttpHeaderEnum $name): StringList
    {
        return $this->headers->get($name);
    }

    public function getHeaderLine(HttpHeaderEnum $name): string
    {
        $values = $this->headers->get($name->getValue());

        return $values->implode();
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function setBody(StreamInterface $body): void
    {
        $this->body = $body;
    }
}
