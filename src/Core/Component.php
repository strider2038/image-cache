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

use Strider2038\ImgCache\Application;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class Component 
{
    /**
     * @var \Strider2038\ImgCache\Application
     */
    private $app;
    
    public function __construct(Application $app) 
    {
        $this->app = $app;
    }
    
    public function getApp(): Application 
    {
        return $this->app;
    }
}
