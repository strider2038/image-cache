<?php

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Exception\ApplicationException;

/**
 * Description of Security
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Security extends Component 
{
    
    /** @var string */
    private $accessToken;
    
    public function setAccessToken(string $token): void 
    {
        if ($this->accessToken !== null) {
            throw new ApplicationException('Access token is already set');
        }
        if (strlen($token) < 32) {
            throw new ApplicationException('Access token is insecure');
        }
        $this->accessToken = $token;
    }
    
    public function isTokenValid(): bool 
    {
        $auth = $this->getApp()->request->getHeader(
            Request::HEADER_AUTHORIZATION
        );
        if (empty($auth)) {
            return false;
        }
        
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
