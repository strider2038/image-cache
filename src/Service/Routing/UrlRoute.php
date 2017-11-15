<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service\Routing;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class UrlRoute
{
    /** @var string */
    private $controllerId;

    /** @var string */
    private $url;

    public function __construct(string $controllerId, string $url)
    {
        $this->controllerId = $controllerId;
        $this->url = $url;
    }

    public function getControllerId(): string
    {
        return $this->controllerId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
