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

use Strider2038\ImgCache\Enum\HttpMethodEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Request extends Message implements RequestInterface
{
    /** @var HttpMethodEnum */
    private $method;

    /** @var UriInterface */
    private $uri;

    public function __construct(HttpMethodEnum $method, UriInterface $uri)
    {
        parent::__construct();
        $this->method = $method;
        $this->uri = $uri;
    }

    public function getMethod(): HttpMethodEnum
    {
        return $this->method;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri): RequestInterface
    {
        $request = new self($this->method, $uri);
        $request->setProtocolVersion($this->protocolVersion);
        $request->setHeaders($this->headers);
        $request->setBody($this->body);

        return $request;
    }
}
