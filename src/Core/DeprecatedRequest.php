<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

/**
 * @deprecated
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class DeprecatedRequest implements DeprecatedRequestInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    
    const HEADER_AUTHORIZATION = 'HTTP_AUTHORIZATION';
    
    /** @var string */
    private $method;

    /** @var string */
    private $requestUri;

    public function __construct() 
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? '');

        if (in_array($method, $this->getAvailableMethods())) {
            $this->method = $method;
        }
    }
    
    public function getMethod(): ? string 
    {
        return $this->method;
    }
    
    public function getHeader(string $key): ? string 
    {
        if (!in_array($key, $this->getAvailableHeaders())) {
            return null;
        }

        return $_SERVER[$key] ?? null;
    }
    
    public function getUrl(int $component = -1): string
    {
        if ($this->requestUri === null) {
            $this->requestUri = $_SERVER['REQUEST_URI'];
        }

        if ($component < 0) {
            return $this->requestUri;
        }

        return parse_url($this->requestUri, $component);
    }

    public function getBody()
    {
        // @todo implement getBody()
    }

    /**
     * @return string[]
     */
    public function getAvailableMethods(): array
    {
        return [
            self::METHOD_GET,
            self::METHOD_POST,
            self::METHOD_PUT,
            self::METHOD_PATCH,
            self::METHOD_DELETE,
        ];
    }
    
    /**
     * @return string[]
     */
    public function getAvailableHeaders(): array
    {
        return [
            self::HEADER_AUTHORIZATION,
        ];
    }
}
