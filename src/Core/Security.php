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

use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Enum\HttpHeader;
use Strider2038\ImgCache\Exception\ApplicationException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Security implements SecurityInterface
{
    /** @var RequestInterface */
    private $request;
    
    /** @var string */
    private $accessToken;
    
    public function __construct(RequestInterface $request, string $token)
    {
        if (strlen($token) < 32) {
            throw new ApplicationException('Access token is insecure');
        }
        $this->request = $request;
        $this->accessToken = $token;
    }

    public function isTokenValid(): bool 
    {
        $headerName = new HttpHeader(HttpHeader::AUTHORIZATION);
        $authenticationData = $this->request->getHeaderLine($headerName);
        if (preg_match('/^Bearer\s+(.*?)$/', $authenticationData, $matches)) {
            return $matches[1] === $this->accessToken;
        }
        
        return false;
    }

    /** @todo add referrer security control */
    public function isReferrerValid(): bool 
    {
        return true;
    }
    
    public function isAuthorized(): bool 
    {
        return $this->isTokenValid() && $this->isReferrerValid();
    }
}
