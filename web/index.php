<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require(__DIR__ . '/../vendor/autoload.php');

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/..'));
$loader->load('config/services-web.yml');

$app = new \Strider2038\ImgCache\Application($container);
$app->run();