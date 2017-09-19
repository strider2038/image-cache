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

use Strider2038\ImgCache\Core\NullStream;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\HttpHeader;
use Strider2038\ImgCache\Enum\HttpProtocolVersion;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Message implements MessageInterface
{
    /** @var HttpProtocolVersion */
    protected $protocolVersion;

    /** @var HeaderCollection */
    protected $headers;

    /** @var StreamInterface */
    protected $body;

    public function __construct()
    {
        $this->protocolVersion = new HttpProtocolVersion(HttpProtocolVersion::V1_1);
        $this->headers = new HeaderCollection();
        $this->body = new NullStream();
    }

    public function getProtocolVersion(): HttpProtocolVersion
    {
        return $this->protocolVersion;
    }

    public function setProtocolVersion(HttpProtocolVersion $protocolVersion)
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

    public function hasHeader(HttpHeader $name): bool
    {
        return $this->headers->containsKey($name);
    }

    public function getHeader(HttpHeader $name): HeaderValueCollection
    {
        return $this->headers->get($name);
    }

    public function getHeaderLine(HttpHeader $name): string
    {
        $values = $this->headers->get($name);

        return implode(',', $values->toArray());
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