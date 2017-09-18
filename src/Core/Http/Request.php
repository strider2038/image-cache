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

use Strider2038\ImgCache\Enum\HttpMethod;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Request extends Message implements RequestInterface
{
    /** @var HttpMethod */
    private $method;

    /** @var UriInterface */
    private $uri;

    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    public function setMethod(HttpMethod $method): void
    {
        $this->method = $method;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function setUri(UriInterface $uri): void
    {
        $this->uri = $uri;
    }
}
