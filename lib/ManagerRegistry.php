<?php

namespace CMSilex;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Proxy\Proxy;
use Symfony\Component\Intl\Exception\NotImplementedException;

class ManagerRegistry extends AbstractManagerRegistry
{
    protected $services;

    public function __construct(EntityManager $em)
    {
        parent::__construct(
            null,
            [],
            ['em' => 'em'],
            null,
            'em',
            Proxy::class
        );
        $this->services = new ArrayCollection();
        $this->addService('em', $em);
    }

    protected function addService ($key, $service)
    {
        $this->services->set($key, $service);
    }

    /**
     * Fetches/creates the given services.
     *
     * A service in this context is connection or a manager instance.
     *
     * @param string $name The name of the service.
     *
     * @return object The instance of the given service.
     */
    protected function getService($name)
    {
        return $this->services->get($name);
    }

    /**
     * Resets the given services.
     *
     * A service in this context is connection or a manager instance.
     *
     * @param string $name The name of the service.
     *
     * @return void
     */
    protected function resetService($name)
    {
        dump("reset");
    }

    public function getAliasNamespace($alias)
    {
        throw new NotImplementedException('Aliases arent implemented');
    }
}