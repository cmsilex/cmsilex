<?php

namespace CMSilex\ServiceProviders;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ORMServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['orm.paths'] = function () {
            return [
                __DIR__ . '/../Entities/'
            ];
        };

        $pimple['orm.cache_dir'] = 'storage/framework/cache/orm';

        $pimple['config.database'] = [
            'driver'   => 'pdo_sqlite'
        ];

        $pimple['em'] = function () use ($pimple) {

            $config = Setup::createAnnotationMetadataConfiguration($pimple['orm.paths'], $pimple['debug']);
            $namingStrategy = new UnderscoreNamingStrategy();
            $config->setNamingStrategy($namingStrategy);
            $config->setProxyDir($pimple['orm.cache_dir']);

            $entityManager = EntityManager::create($pimple['config.database'], $config);
            return $entityManager;
        };
    }
}