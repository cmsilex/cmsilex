<?php

namespace CMSilex\ServiceProviders;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ORMServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['orm.paths'] = function () {
            return [
                __DIR__ . '/../Entities/'
            ];
        };

        $app['orm.cache_dir'] = 'storage/framework/cache/orm';

        $app['config.database'] = [
            'driver'   => 'pdo_sqlite',
            'path' => __DIR__ . '/../../../../../db.sqlite'
        ];

        $app['em'] = function () use ($app) {

            $config = Setup::createAnnotationMetadataConfiguration($app['orm.paths'], $app['debug']);
            $namingStrategy = new UnderscoreNamingStrategy();
            $config->setNamingStrategy($namingStrategy);
            $config->setProxyDir($app['orm.cache_dir']);

            $entityManager = EntityManager::create($app['config.database'], $config);
            return $entityManager;
        };
    }

    public function boot(Application $app)
    {

    }
}