<?php

namespace CMSilex;

use CMSilex\ServiceProviders\ORMServiceProvider;
use Silex\Application;

class CMSilex extends Application
{
    public function bootstrap()
    {
        $app = $this;
        $app->register(new ORMServiceProvider());
    }
}
