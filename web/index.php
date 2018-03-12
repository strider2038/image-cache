<?php

use Strider2038\ImgCache\ApplicationFactory;
use Strider2038\ImgCache\Core\ApplicationParameters;

$applicationDirectory = __DIR__ . '/..';

require $applicationDirectory . '/vendor/autoload.php';

$parameters = new ApplicationParameters(
    $applicationDirectory,
    $_SERVER
);

$application = ApplicationFactory::createApplication($parameters);
$application->run();
