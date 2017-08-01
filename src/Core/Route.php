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
class Route 
{
    /** @var string */
    private $controllerId;

    /** @var string */
    private $actionId;

    /** @var string */
    private $location;

    public function __construct(string $controllerId, string $actionId, string $location)
    {
        $this->controllerId = $controllerId;
        $this->actionId = $actionId;
        $this->location = $location;
    }
    
    public function getControllerId(): string
    {
        return $this->controllerId;
    }
    
    public function getActionId(): string
    {
        return $this->actionId;
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}
