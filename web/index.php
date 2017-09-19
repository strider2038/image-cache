<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$applicationDirectory = __DIR__ . '/..';

require($applicationDirectory . '/vendor/autoload.php');

$container = new ContainerBuilder();

$loader = new YamlFileLoader($container, new FileLocator($applicationDirectory));
$loader->load('config/web.yml');
$container->setParameter('app.directory', $applicationDirectory);
$container->setParameter('serverConfiguration', $_SERVER);

$app = new \Strider2038\ImgCache\Application($container);
$app->run();