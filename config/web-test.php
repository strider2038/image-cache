<?php

return [
    'id' => 'ImgCacheTest',
    'components' => [
        'router' => function($app) {
            return new \Strider2038\ImgCache\Service\Router($app);
        }
    ],
    'params' => [
        'debug' => false,
    ],
];