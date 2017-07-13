<?php

require(__DIR__ . '/../vendor/autoload.php');

$config = require(__DIR__ . '/../config/web.php');

$app = new \Strider2038\ImgCache\Application($config);
$app->run();