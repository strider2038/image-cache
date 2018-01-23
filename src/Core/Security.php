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
use Strider2038\ImgCache\Enum\HttpHeaderEnum;
use Strider2038\ImgCache\Exception\ApplicationException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 * @deprecated
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
        $isTokenValid = false;
        $headerName = new HttpHeaderEnum(HttpHeaderEnum::AUTHORIZATION);
        $authenticationData = $this->request->getHeaderLine($headerName);

        if (preg_match('/^Bearer\s+(.*?)$/', $authenticationData, $matches)) {
            $isTokenValid = $matches[1] === $this->accessToken;
        }
        
        return $isTokenValid;
    }

    public function isAuthorized(): bool
    {
        return $this->isTokenValid();
    }
}
