<?php

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Application;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Object {
    /**
     * @var \Strider2038\ImgCache\Application
     */
    private $app;
    
    public function setApp(Application $app) {
        $this->app = $app;
        return $this;
    }
    
    public function getApp(): Application {
        return $this->app;
    }
}
