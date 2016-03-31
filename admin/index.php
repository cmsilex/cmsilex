<?php

require __DIR__ . "/../vendor/autoload.php";

$app = new \CMSilex\CMSilex();

$app->bootstrap();

$app->run();