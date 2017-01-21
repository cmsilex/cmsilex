<?php

namespace CMSilex\Tools\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class DeployPublicAssetsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cmsilex:deploy-assets')
            ->setDescription("Move the cmsilex assets to the public directory.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();

        $fs->symlink(__DIR__ . '/../../../../resources/public', __DIR__ .'/../../../../../../../public/cmsilex');
    }
}