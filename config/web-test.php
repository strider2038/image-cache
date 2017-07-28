<?php

return [
    'id' => 'ImgCacheTest',
    'components' => [
        'router' => function($app) {
            return new \Strider2038\ImgCache\Service\Router($app);
        },
        'imgcache' => function($app) {
            return new \Strider2038\ImgCache\Imaging\ImageCache(
                __DIR__ . '/../web-test',
                $app->imageSource,
                $app->transformationsFactory,
                $app->processingEngine
            );
        },
        'transformationsFactory' => \Strider2038\ImgCache\Imaging\Transformation\TransformationsFactory::class,
        'processingEngine' => \Strider2038\ImgCache\Imaging\Processing\Adapter\ImagickEngine::class,
        'imageSource' => function(\Strider2038\ImgCache\Application $app) {
            return new \Strider2038\ImgCache\Imaging\Source\FilesystemSource(
                $app->temporaryFileManager, 
                __DIR__ . '/../isource-test'
            );
        },
    ],
    'params' => [
        'debug' => false,
    ],
];