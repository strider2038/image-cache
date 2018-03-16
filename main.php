<?php

use Strider2038\ImgCache\ApplicationFactory;
use Strider2038\ImgCache\Core\ApplicationParameters;

$applicationDirectory = __DIR__;

require $applicationDirectory . '/vendor/autoload.php';

$parameters = new ApplicationParameters(
    $applicationDirectory,
    $_SERVER
);

try {
    $application = ApplicationFactory::createApplication($parameters);
    $application->run();
} catch (\Throwable $exception) {
    header('HTTP/1.1 500 Internal server error');
    echo $exception;
}
