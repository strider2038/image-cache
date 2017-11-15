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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Route 
{
    /** @var string */
    private $controllerId;

    /** @var string */
    private $actionId;

    /** @var RequestInterface */
    private $request;

    public function __construct(string $controllerId, string $actionId, RequestInterface $request)
    {
        $this->controllerId = $controllerId;
        $this->actionId = $actionId;
        $this->request = $request;
    }
    
    public function getControllerId(): string
    {
        return $this->controllerId;
    }
    
    public function getActionId(): string
    {
        return $this->actionId;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
