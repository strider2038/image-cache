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
        if (empty($this->accessToken)) {
            throw new ApplicationException('Access token is not set');
        }
        $auth = $this->request->getHeader(Request::HEADER_AUTHORIZATION);
        if ($auth !== null && preg_match('/^Bearer\s+(.*?)$/', $auth, $matches)) {
            return $matches[1] === $this->accessToken;
        }
        
        return false;
    }
    
    public function isReferrerValid(): bool 
    {
        return true;
    }
    
    public function isAuthorized(): bool 
    {
        return $this->isTokenValid() && $this->isReferrerValid();
    }
}
