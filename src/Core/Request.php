<?php

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Exception\RequestException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Request extends Component implements RequestInterface 
{
    
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    
    const HEADER_AUTHORIZATION = 'HTTP_AUTHORIZATION';
    
    /** @var string */
    private $method;


    public function __construct() 
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        if (!in_array($method, static::getAvailableMethods())) {
            throw new RequestException('Unknown request method');
        }
        $this->method = $method;
    }
    
    public function getMethod(): string 
    {
        return $this->method;
    }
    
    public function getHeader(string $key): ?string 
    {
        return $_SERVER[$key] ?? null;
    }
    
    public static function getAvailableMethods(): array
    {
        return [
            self::METHOD_GET,
            self::METHOD_POST,
            self::METHOD_PUT,
            self::METHOD_PATCH,
            self::METHOD_DELETE,
        ];
    }
}
