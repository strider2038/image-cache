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
class Uri implements UriInterface
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getScheme(): string
    {
        return parse_url($this->value, PHP_URL_SCHEME);
    }

    public function getAuthority(): string
    {
        $userInfo = $this->getUserInfo();
        $port = $this->getPort();

        return ($userInfo ? $userInfo . '@' : '') . $this->getHost() . ($port === null ? '' : (':' . $port));
    }

    public function getUserInfo(): string
    {
        $user = parse_url($this->value, PHP_URL_USER) ?? '';
        $password = parse_url($this->value, PHP_URL_PASS);

        return $user . ($password ? ':' . $password : '');
    }

    public function getHost(): string
    {
        return parse_url($this->value, PHP_URL_HOST) ?? '';
    }

    public function getPort(): ? int
    {
        return parse_url($this->value, PHP_URL_PORT) ?? null;
    }

    public function getPath(): string
    {
        return parse_url($this->value, PHP_URL_PATH) ?? '';
    }

    public function getQuery(): string
    {
        return parse_url($this->value, PHP_URL_QUERY) ?? '';
    }

    public function getFragment(): string
    {
        return parse_url($this->value, PHP_URL_FRAGMENT) ?? '';
    }

    public function __toString()
    {
        return $this->value;
    }
}