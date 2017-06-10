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
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class Response implements ResponseInterface 
{
    
    const HTTP_CODE_OK                     = 200;
    const HTTP_CODE_CREATED                = 201;
    const HTTP_CODE_ACCEPTED               = 202;
    const HTTP_CODE_RESET_CONTENT          = 205;
    const HTTP_CODE_MOVED_PERMANENTLY      = 301;
    const HTTP_CODE_FOUND                  = 302;
    const HTTP_CODE_NOT_MODIFIED           = 304;
    const HTTP_CODE_BAD_REQUEST            = 400;
    const HTTP_CODE_UNAUTHORIZED           = 401;
    const HTTP_CODE_FORBIDDEN              = 403;
    const HTTP_CODE_NOT_FOUND              = 404;
    const HTTP_CODE_METHOD_NOT_ALLOWED     = 405;
    const HTTP_CODE_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_CODE_INTERNAL_SERVER_ERROR  = 500;
    
    public static function getAvailableHttpStatuses(): array
    {
        return [
            self::HTTP_CODE_OK                     => 'OK',
            self::HTTP_CODE_CREATED                => 'Created',
            self::HTTP_CODE_ACCEPTED               => 'Accepted',
            self::HTTP_CODE_RESET_CONTENT          => 'Reset Content',
            self::HTTP_CODE_MOVED_PERMANENTLY      => 'Moved Permanently',
            self::HTTP_CODE_FOUND                  => 'Found',
            self::HTTP_CODE_NOT_MODIFIED           => 'Not Modified',
            self::HTTP_CODE_BAD_REQUEST            => 'Bad Request',
            self::HTTP_CODE_UNAUTHORIZED           => 'Unauthorized',
            self::HTTP_CODE_FORBIDDEN              => 'Forbidden',
            self::HTTP_CODE_NOT_FOUND              => 'Not Found',
            self::HTTP_CODE_METHOD_NOT_ALLOWED     => 'Method Not Allowed',
            self::HTTP_CODE_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
            self::HTTP_CODE_INTERNAL_SERVER_ERROR  => 'Internal Server Error',
        ];
    }
    
    public static function getAvailableHttpCodes(): array
    {
        return array_keys(static::getAvailableHttpStatuses());
    }

    public static function getHttpStatusText(int $httpCode): string
    {
        return static::getAvailableHttpStatuses()[$httpCode];
    }
    
    /** @var int */
    private $httpCode;
    
    /** @var string */
    private $httpVersion;
    
    /** @var string */
    private $charset = 'utf-8';
    
    /** @var bool */
    private $isSent = false;
    
    /** @var array */
    private $headers = [];
    
    public function __construct(int $httpCode) 
    {
        $this->httpCode = in_array($httpCode, static::getAvailableHttpCodes()) 
            ? $httpCode
            : self::HTTP_CODE_INTERNAL_SERVER_ERROR;
        
        if ($this->httpVersion === null) {
            if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
                $this->httpVersion = '1.0';
            } else {
                $this->httpVersion = '1.1';
            }
        }
    }
    
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getHttpVersion(): string
    {
        return $this->httpVersion;
    }
        
    public function setHeader(string $name, string $value): Response
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    public function send(): void 
    {
        if ($this->isSent) {
            return;
        }
        $this->sendHeaders();
        $this->sendContent();
        $this->isSent = true;
    }
    
    public function isSent(): bool
    {
        return $this->isSent;
    }
    
    protected function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }
        if (!empty($this->headers)) {
            foreach ($this->headers as $name => $value) {
                $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
                header("$name: $value");
            }
        }
        header(
            "HTTP/{$this->httpVersion} {$this->httpCode} " 
            . $this->getHttpStatusText($this->httpCode)
        );
    }
    
    abstract protected function sendContent(): void;

}
