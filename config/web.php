<?php

$configWeb = [
    'id' => 'ImgCache',
    'components' => [

        // service
        'router' => function($app) {
            return new \Strider2038\ImgCache\Service\Router($app, $app->imageValidator);
        },

        // imaging
        'imageCache' => function($app) {
            return new \Strider2038\ImgCache\Imaging\ImageCache(
                __DIR__ . '/../web',
                $app->imageFactory,
                $app->thumbnailImageExtractor
            );
        },

        // imaging/image
        'imageFactory' => function($app) {
            return new \Strider2038\ImgCache\Imaging\Image\ImageFactory(
                $app->saveOptionsFactory,
                $app->imageValidator
            );
        },

        // imaging/extraction
        'thumbnailImageExtractor' => function($app) {
            return new \Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageExtractor(
                $app->thumbnailKeyParser,
                $app->filesystemSourceAccessor,
                $app->thumbnailProcessingConfigurationParser,
                $app->imageProcessor
            );
        },

        // imaging/parsing
        'thumbnailKeyParser' => function($app) {
            return new \Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKeyParser(
                $app->keyValidator,
                $app->imageValidator
            );
        },
        'thumbnailProcessingConfigurationParser' => function($app) {
            return new \Strider2038\ImgCache\Imaging\Parsing\Processing\ThumbnailProcessingConfigurationParser(
                $app->transformationsFactory,
                $app->saveOptionsFactory,
                $app->saveOptionsConfigurator
            );
        },

        // imaging/processing
        'saveOptionsFactory' => \Strider2038\ImgCache\Imaging\Processing\SaveOptionsFactory::class,
        'saveOptionsConfigurator' => \Strider2038\ImgCache\Imaging\Parsing\SaveOptionsConfigurator::class,
        'imageProcessor' => function($app) {
            return new \Strider2038\ImgCache\Imaging\Processing\ImageProcessor(
                $app->imagickEngine
            );
        },
        'imagickEngine' => \Strider2038\ImgCache\Imaging\Processing\Adapter\ImagickEngine::class,

        // imaging/transformation
        'transformationsFactory' => \Strider2038\ImgCache\Imaging\Transformation\TransformationsFactory::class,

        // imaging/source
        'filesystemSourceAccessor' => function($app) {
            return new \Strider2038\ImgCache\Imaging\Source\Accessor\FilesystemSourceAccessor(
                $app->filesystemSource,
                $app->directKeyMapper
            );
        },
        'filesystemSource' => function($app) {
            return new \Strider2038\ImgCache\Imaging\Source\FilesystemSource(
                __DIR__ . '/../tests/assets',
                $app->imageFactory
            );
        },
        'directKeyMapper' => \Strider2038\ImgCache\Imaging\Source\Mapping\DirectKeyMapper::class,

        // imaging/validation
        'imageValidator' => \Strider2038\ImgCache\Imaging\Validation\ImageValidator::class,
        'keyValidator' => \Strider2038\ImgCache\Imaging\Validation\KeyValidator::class,

    ],
    'params' => [
        'debug' => false,
    ],
];

$configWebLocalFilename = __DIR__ . '/web-local.php';
$configWebLocal = file_exists($configWebLocalFilename) ? require 'web-local.php' : [];

return array_replace_recursive($configWeb, $configWebLocal);