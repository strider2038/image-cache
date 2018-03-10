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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class BearerAccessControl implements AccessControlInterface
{
    /** @var string */
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function canHandleRequest(RequestInterface $request): bool
    {
        $authorizationHeaderName = new HttpHeaderEnum(HttpHeaderEnum::AUTHORIZATION);
        $authorizationHeaderValue = $request->getHeaderLine($authorizationHeaderName);

        return $this->isTokenValidForAuthorizationHeader($authorizationHeaderValue);
    }

    private function isTokenValidForAuthorizationHeader($authorizationHeaderValue): bool
    {
        $isTokenValid = false;

        if (preg_match('/^Bearer\s+(.*?)$/', $authorizationHeaderValue, $matches)) {
            $isTokenValid = $matches[1] === $this->token;
        }

        return $isTokenValid;
    }
}
