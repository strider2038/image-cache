<?php

return [
    'id' => 'ImgCacheTest',
    'components' => [
        'router' => function($app) {
            return new \Strider2038\ImgCache\Service\Router($app);
        },
        'imgcache' => function($app) {
            return new \Strider2038\ImgCache\Imaging\ImageCache($app);
        },
        'transformationsFactory' => function($app) {
            return new \Strider2038\ImgCache\Imaging\Transformation\TransformationsFactory($app);
        },
    ],
    'params' => [
        'debug' => false,
    ],
];