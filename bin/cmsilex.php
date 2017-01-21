<?php

// application.php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new \CMSilex\Tools\Console\Command\DeployPublicAssetsCommand());

$application->run();