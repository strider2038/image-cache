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
class Response extends Component implements ResponseInterface 
{
    
    const HTTP_CODE_OK = 200;
    const HTTP_CODE_NOT_FOUND = 404;
    const HTTP_CODE_INTERNAL_SERVER_ERROR = 500;
    
    private $httpCode;
    
    public function __construct(int $httpCode) 
    {
        if (in_array($httpCode, static::getAvailableHttpCodes())) {
            $this->httpCode = $httpCode;
        } else {
            $this->httpCode = self::HTTP_CODE_INTERNAL_SERVER_ERROR;
        }
    }
    
    public function send(): void 
    {
        echo "Http: " . $this->httpCode;
    }
    
    public static function getAvailableHttpCodes(): array
    {
        return [
            self::HTTP_CODE_OK,
            self::HTTP_CODE_NOT_FOUND,
            self::HTTP_CODE_INTERNAL_SERVER_ERROR,
        ];
    }
}
