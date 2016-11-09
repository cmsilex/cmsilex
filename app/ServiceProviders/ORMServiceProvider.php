<?php

namespace CMSilex\ServiceProviders;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ORMServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['orm.paths'] = function ($container) {
            return [
                __DIR__ . '/../Entities/'
            ];
        };

        $container['orm.cache_dir'] = __DIR__ . '/../../../../../storage/framework/cache/orm';

        $container['config.database'] = $container['config']['db'];

        $container['em'] = function () use ($container) {

            $config = Setup::createAnnotationMetadataConfiguration($container['orm.paths'], $container['debug']);
            $namingStrategy = new UnderscoreNamingStrategy();
            $config->setNamingStrategy($namingStrategy);
            $config->setProxyDir($container['orm.cache_dir']);

            $entityManager = EntityManager::create($container['config.database'], $config);
            
            return $entityManager;
        };
    }
}