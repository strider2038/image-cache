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

use Strider2038\ImgCache\Core\Http\UriInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class UrlRoute
{
    /** @var string */
    private $controllerId;

    /** @var UriInterface */
    private $uri;

    public function __construct(string $controllerId, UriInterface $uri)
    {
        $this->controllerId = $controllerId;
        $this->uri = $uri;
    }

    public function getControllerId(): string
    {
        return $this->controllerId;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }
}
