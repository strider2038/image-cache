<?php

$configWeb = [
    'id' => 'ImgCache',
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
        'imageSource' => function($app) {
            return new Strider2038\ImgCache\Imaging\Source\FileSource(
                $temporaryFilesManager, 
                $baseDirectory
            );
        }
    ],
    'params' => [
        'debug' => false,
    ],
];

$configWebLocalFilename = __DIR__ . '/web-local.php';
$configWebLocal = file_exists($configWebLocalFilename) ? require 'web-local.php' : [];

return array_replace_recursive($configWeb, $configWebLocal);