<?php

$configWeb = [
    'id' => 'ImgCache',
    'components' => [
        'router' => function($app) {
            return new \Strider2038\ImgCache\Service\Router($app);
        },
        'imgcache' => function($app) {
            return new \Strider2038\ImgCache\Imaging\ImageCache(
                $app->imageSource,
                $app->transformationsFactory
            );
        },
        'transformationsFactory' => \Strider2038\ImgCache\Imaging\Transformation\TransformationsFactory::class,
        'imageSource' => function(\Strider2038\ImgCache\Application $app) {
            return new \Strider2038\ImgCache\Imaging\Source\FileSource(
                $app->temporaryFileManager, 
                '/services/imgcache/isource'
            );
        },
    ],
    'params' => [
        'debug' => false,
    ],
];

$configWebLocalFilename = __DIR__ . '/web-local.php';
$configWebLocal = file_exists($configWebLocalFilename) ? require 'web-local.php' : [];

return array_replace_recursive($configWeb, $configWebLocal);